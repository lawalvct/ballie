<?php

namespace App\Http\Controllers\Api\Tenant\Procurement;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of purchase orders.
     */
    public function index(Request $request, Tenant $tenant)
    {
        $query = PurchaseOrder::where('tenant_id', $tenant->id)
            ->with(['vendor', 'creator']);

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->get('vendor_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('lpo_date', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('lpo_date', '<=', $request->get('date_to'));
        }

        $perPage = (int) $request->get('per_page', 15);
        $purchaseOrders = $query
            ->latest('lpo_date')
            ->paginate($perPage);

        $purchaseOrders->getCollection()->transform(function (PurchaseOrder $purchaseOrder) {
            return $this->formatPurchaseOrder($purchaseOrder);
        });

        $statistics = [
            'total_purchase_orders' => PurchaseOrder::where('tenant_id', $tenant->id)->count(),
            'draft_purchase_orders' => PurchaseOrder::where('tenant_id', $tenant->id)->where('status', 'draft')->count(),
            'sent_purchase_orders' => PurchaseOrder::where('tenant_id', $tenant->id)->where('status', 'sent')->count(),
            'confirmed_purchase_orders' => PurchaseOrder::where('tenant_id', $tenant->id)->where('status', 'confirmed')->count(),
            'received_purchase_orders' => PurchaseOrder::where('tenant_id', $tenant->id)->where('status', 'received')->count(),
            'total_value' => (float) PurchaseOrder::where('tenant_id', $tenant->id)->sum('total_amount'),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Purchase orders retrieved successfully',
            'data' => $purchaseOrders,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Get form data for creating a purchase order.
     */
    public function create(Tenant $tenant)
    {
        $vendors = Vendor::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->orderBy('company_name')
            ->orderBy('first_name')
            ->get()
            ->map(function (Vendor $vendor) {
                return [
                    'id' => $vendor->id,
                    'name' => $vendor->getFullNameAttribute(),
                    'email' => $vendor->email,
                ];
            });

        $products = Product::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->with('primaryUnit')
            ->orderBy('name')
            ->get()
            ->map(function (Product $product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'purchase_rate' => $product->purchase_rate,
                    'unit' => $product->primaryUnit->symbol ?? 'Pcs',
                ];
            });

        $lpoNumber = PurchaseOrder::generateLpoNumber($tenant->id);

        return response()->json([
            'success' => true,
            'message' => 'Purchase order form data retrieved successfully',
            'data' => [
                'vendors' => $vendors,
                'products' => $products,
                'lpo_number' => $lpoNumber,
            ],
        ]);
    }

    /**
     * Store a new purchase order.
     */
    public function store(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), $this->rules());

        $validator->after(function ($validator) use ($request, $tenant) {
            $vendorId = $request->get('vendor_id');
            if ($vendorId && !Vendor::where('tenant_id', $tenant->id)->where('id', $vendorId)->exists()) {
                $validator->errors()->add('vendor_id', 'Selected vendor is invalid.');
            }

            $productIds = collect($request->get('items', []))
                ->pluck('product_id')
                ->filter()
                ->unique()
                ->all();

            if (!empty($productIds)) {
                $invalidProduct = Product::whereIn('id', $productIds)
                    ->where(function ($query) use ($tenant) {
                        $query->where('tenant_id', '!=', $tenant->id)
                            ->orWhere('is_active', false);
                    })
                    ->exists();

                if ($invalidProduct) {
                    $validator->errors()->add('items', 'One or more products are invalid.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $lpoNumber = PurchaseOrder::generateLpoNumber($tenant->id);

            $purchaseOrder = PurchaseOrder::create([
                'tenant_id' => $tenant->id,
                'vendor_id' => $request->get('vendor_id'),
                'lpo_number' => $lpoNumber,
                'lpo_date' => $request->get('lpo_date'),
                'expected_delivery_date' => $request->get('expected_delivery_date'),
                'status' => $request->get('action') === 'send' ? 'sent' : 'draft',
                'notes' => $request->get('notes'),
                'terms_conditions' => $request->get('terms_conditions'),
                'created_by' => Auth::id(),
            ]);

            $subtotal = 0;
            $taxAmount = 0;
            $discountAmount = 0;

            foreach ($request->get('items', []) as $item) {
                $product = Product::where('tenant_id', $tenant->id)->findOrFail($item['product_id']);
                $itemTotal = ($item['quantity'] * $item['unit_price']) - ($item['discount'] ?? 0);
                $itemTax = $itemTotal * (($item['tax_rate'] ?? 0) / 100);
                $total = $itemTotal + $itemTax;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $product->id,
                    'description' => $item['description'] ?? $product->name,
                    'quantity' => $item['quantity'],
                    'unit' => $product->primaryUnit->symbol ?? 'Pcs',
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'] ?? 0,
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'total' => $total,
                ]);

                $subtotal += $item['quantity'] * $item['unit_price'];
                $discountAmount += $item['discount'] ?? 0;
                $taxAmount += $itemTax;
            }

            $purchaseOrder->update([
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $subtotal - $discountAmount + $taxAmount,
            ]);

            DB::commit();

            $purchaseOrder->load(['vendor', 'items.product', 'creator', 'updater']);

            return response()->json([
                'success' => true,
                'message' => 'Purchase order created successfully',
                'data' => [
                    'purchase_order' => $this->formatPurchaseOrder($purchaseOrder, true),
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the purchase order.',
            ], 500);
        }
    }

    /**
     * Show a purchase order.
     */
    public function show(Tenant $tenant, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase order not found',
            ], 404);
        }

        $purchaseOrder->load(['vendor', 'items.product', 'creator', 'updater']);

        return response()->json([
            'success' => true,
            'message' => 'Purchase order retrieved successfully',
            'data' => [
                'purchase_order' => $this->formatPurchaseOrder($purchaseOrder, true),
            ],
        ]);
    }

    /**
     * Download purchase order as PDF.
     */
    public function pdf(Tenant $tenant, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase order not found',
            ], 404);
        }

        try {
            $purchaseOrder->load(['vendor', 'items.product']);
            $pdf = Pdf::loadView('tenant.procurement.purchase-orders.pdf', compact('tenant', 'purchaseOrder'));

            return $pdf->download($purchaseOrder->lpo_number . '.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate PDF.',
            ], 500);
        }
    }

    /**
     * Email purchase order with PDF attachment.
     */
    public function email(Request $request, Tenant $tenant, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase order not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'to' => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $purchaseOrder->load(['vendor', 'items.product']);
            $pdf = Pdf::loadView('tenant.procurement.purchase-orders.pdf', compact('tenant', 'purchaseOrder'));

            Mail::send('emails.purchase-order', [
                'purchaseOrder' => $purchaseOrder,
                'tenant' => $tenant,
                'emailMessage' => $request->get('message'),
            ], function ($mail) use ($request, $purchaseOrder, $pdf) {
                $mail->to($request->get('to'))
                    ->subject($request->get('subject'))
                    ->attachData($pdf->output(), $purchaseOrder->lpo_number . '.pdf', ['mime' => 'application/pdf']);
            });

            if ($purchaseOrder->status === 'draft') {
                $purchaseOrder->update(['status' => 'sent']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Purchase order sent successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email',
            ], 500);
        }
    }

    /**
     * Search vendors for LPO.
     */
    public function searchVendors(Request $request, Tenant $tenant)
    {
        $query = trim($request->get('q', ''));

        $vendors = Vendor::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->where(function ($q) use ($query) {
                $q->where('company_name', 'like', "%{$query}%")
                    ->orWhere('first_name', 'like', "%{$query}%")
                    ->orWhere('last_name', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(function (Vendor $vendor) {
                return [
                    'id' => $vendor->id,
                    'name' => $vendor->getFullNameAttribute(),
                    'email' => $vendor->email,
                ];
            });

        return response()->json($vendors);
    }

    /**
     * Search products for LPO items.
     */
    public function searchProducts(Request $request, Tenant $tenant)
    {
        $query = trim($request->get('q', ''));

        $products = Product::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%");
            })
            ->with('primaryUnit')
            ->limit(15)
            ->get()
            ->map(function (Product $product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'purchase_rate' => $product->purchase_rate,
                    'unit' => $product->primaryUnit->symbol ?? 'Pcs',
                ];
            });

        return response()->json($products);
    }

    private function rules(): array
    {
        return [
            'vendor_id' => 'required|exists:vendors,id',
            'lpo_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after:lpo_date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0',
        ];
    }

    private function formatPurchaseOrder(PurchaseOrder $purchaseOrder, bool $withItems = false): array
    {
        $data = [
            'id' => $purchaseOrder->id,
            'lpo_number' => $purchaseOrder->lpo_number,
            'lpo_date' => $purchaseOrder->lpo_date?->format('Y-m-d'),
            'expected_delivery_date' => $purchaseOrder->expected_delivery_date?->format('Y-m-d'),
            'vendor_id' => $purchaseOrder->vendor_id,
            'vendor_name' => $purchaseOrder->vendor?->getFullNameAttribute(),
            'status' => $purchaseOrder->status,
            'notes' => $purchaseOrder->notes,
            'terms_conditions' => $purchaseOrder->terms_conditions,
            'subtotal' => (float) ($purchaseOrder->subtotal ?? 0),
            'discount_amount' => (float) ($purchaseOrder->discount_amount ?? 0),
            'tax_amount' => (float) ($purchaseOrder->tax_amount ?? 0),
            'total_amount' => (float) ($purchaseOrder->total_amount ?? 0),
            'created_at' => $purchaseOrder->created_at?->toDateTimeString(),
            'updated_at' => $purchaseOrder->updated_at?->toDateTimeString(),
        ];

        if ($withItems) {
            $data['items'] = $purchaseOrder->items->map(function (PurchaseOrderItem $item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product?->name,
                    'description' => $item->description,
                    'quantity' => (float) $item->quantity,
                    'unit' => $item->unit,
                    'unit_price' => (float) $item->unit_price,
                    'discount' => (float) $item->discount,
                    'tax_rate' => (float) $item->tax_rate,
                    'total' => (float) $item->total,
                ];
            })->values();
        }

        return $data;
    }
}
