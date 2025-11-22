<?php

namespace App\Http\Controllers\Tenant\Procurement;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseOrderController extends Controller
{
    public function index(Tenant $tenant)
    {
        $purchaseOrders = PurchaseOrder::where('tenant_id', $tenant->id)
            ->with(['vendor', 'creator'])
            ->latest('lpo_date')
            ->paginate(15);

        return view('tenant.procurement.purchase-orders.index', compact('tenant', 'purchaseOrders'));
    }

    public function create(Tenant $tenant)
    {
        $vendors = Vendor::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->orderBy('company_name')
            ->orderBy('first_name')
            ->get();

        $products = Product::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->with('primaryUnit')
            ->orderBy('name')
            ->get();

        $lpoNumber = PurchaseOrder::generateLpoNumber($tenant->id);

        return view('tenant.procurement.purchase-orders.create', compact('tenant', 'vendors', 'products', 'lpoNumber'));
    }

    public function store(Request $request, Tenant $tenant)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'lpo_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after:lpo_date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $lpoNumber = PurchaseOrder::generateLpoNumber($tenant->id);

            $purchaseOrder = PurchaseOrder::create([
                'tenant_id' => $tenant->id,
                'vendor_id' => $request->vendor_id,
                'lpo_number' => $lpoNumber,
                'lpo_date' => $request->lpo_date,
                'expected_delivery_date' => $request->expected_delivery_date,
                'status' => $request->action === 'send' ? 'sent' : 'draft',
                'notes' => $request->notes,
                'terms_conditions' => $request->terms_conditions,
                'created_by' => auth()->id(),
            ]);

            $subtotal = 0;
            $taxAmount = 0;
            $discountAmount = 0;

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
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

            return redirect()
                ->route('tenant.procurement.purchase-orders.show', ['tenant' => $tenant->slug, 'purchaseOrder' => $purchaseOrder->id])
                ->with('success', 'Purchase Order created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error creating purchase order: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Tenant $tenant, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->tenant_id !== $tenant->id) {
            abort(404);
        }

        $purchaseOrder->load(['vendor', 'items.product', 'creator', 'updater']);

        return view('tenant.procurement.purchase-orders.show', compact('tenant', 'purchaseOrder'));
    }

    public function pdf(Tenant $tenant, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->tenant_id !== $tenant->id) {
            abort(404);
        }

        $purchaseOrder->load(['vendor', 'items.product']);

        $pdf = Pdf::loadView('tenant.procurement.purchase-orders.pdf', compact('tenant', 'purchaseOrder'));

        return $pdf->download($purchaseOrder->lpo_number . '.pdf');
    }

    public function email(Request $request, Tenant $tenant, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'to' => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        try {
            $purchaseOrder->load(['vendor', 'items.product']);

            $pdf = Pdf::loadView('tenant.procurement.purchase-orders.pdf', compact('tenant', 'purchaseOrder'));

            Mail::send('emails.purchase-order', [
                'purchaseOrder' => $purchaseOrder,
                'tenant' => $tenant,
                'emailMessage' => $request->message,
            ], function ($mail) use ($request, $purchaseOrder, $pdf) {
                $mail->to($request->to)
                     ->subject($request->subject)
                     ->attachData($pdf->output(), $purchaseOrder->lpo_number . '.pdf', ['mime' => 'application/pdf']);
            });

            if ($purchaseOrder->status === 'draft') {
                $purchaseOrder->update(['status' => 'sent']);
            }

            return response()->json(['message' => 'Purchase Order sent successfully']);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send email'], 500);
        }
    }

    public function searchVendors(Request $request, Tenant $tenant)
    {
        $query = trim($request->get('q', ''));

        $vendors = Vendor::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->where(function($q) use ($query) {
                $q->where('company_name', 'like', "%{$query}%")
                  ->orWhere('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(function($vendor) {
                return [
                    'id' => $vendor->id,
                    'name' => $vendor->getFullNameAttribute(),
                    'email' => $vendor->email,
                ];
            });

        return response()->json($vendors);
    }

    public function searchProducts(Request $request, Tenant $tenant)
    {
        $query = trim($request->get('q', ''));

        $products = Product::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->with('primaryUnit')
            ->limit(15)
            ->get()
            ->map(function($product) {
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
}
