<?php

namespace App\Http\Controllers\Api\Tenant\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\LedgerAccount;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class QuotationController extends Controller
{
    /**
     * Display a listing of quotations.
     */
    public function index(Request $request, Tenant $tenant)
    {
        $query = Quotation::where('tenant_id', $tenant->id)
            ->with(['customer', 'vendor', 'customerLedger', 'items']);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('quotation_number', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('company_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('quotation_date', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('quotation_date', '<=', $request->get('date_to'));
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->get('customer_id'));
        }

        $perPage = (int) $request->get('per_page', 15);
        $quotations = $query
            ->orderBy('quotation_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $quotations->getCollection()->transform(function (Quotation $quotation) {
            return $this->formatQuotation($quotation);
        });

        $statistics = [
            'total_quotations' => Quotation::where('tenant_id', $tenant->id)->count(),
            'draft_quotations' => Quotation::where('tenant_id', $tenant->id)->where('status', 'draft')->count(),
            'sent_quotations' => Quotation::where('tenant_id', $tenant->id)->where('status', 'sent')->count(),
            'accepted_quotations' => Quotation::where('tenant_id', $tenant->id)->where('status', 'accepted')->count(),
            'rejected_quotations' => Quotation::where('tenant_id', $tenant->id)->where('status', 'rejected')->count(),
            'expired_quotations' => Quotation::where('tenant_id', $tenant->id)->where('status', 'expired')->count(),
            'converted_quotations' => Quotation::where('tenant_id', $tenant->id)->where('status', 'converted')->count(),
            'total_value' => (float) Quotation::where('tenant_id', $tenant->id)->sum('total_amount'),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Quotations retrieved successfully',
            'data' => $quotations,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Get form data for creating a quotation.
     */
    public function create(Tenant $tenant)
    {
        $products = Product::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->where('is_saleable', true)
            ->with(['primaryUnit'])
            ->orderBy('name')
            ->get()
            ->map(function (Product $product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'sales_rate' => $product->sales_rate,
                    'purchase_rate' => $product->purchase_rate,
                    'current_stock' => $product->current_stock,
                    'unit' => $product->primaryUnit->symbol ?? 'Pcs',
                    'description' => $product->description,
                ];
            });

        $customers = Customer::with('ledgerAccount')
            ->where('tenant_id', $tenant->id)
            ->orderBy('company_name')
            ->orderBy('first_name')
            ->get()
            ->map(function (Customer $customer) {
                $ledger = $customer->ledgerAccount;

                return [
                    'id' => $customer->id,
                    'ledger_account_id' => $ledger?->id,
                    'ledger_account_name' => $ledger?->name,
                    'display_name' => $customer->company_name ?: trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')),
                    'email' => $customer->email,
                ];
            })
            ->filter(function ($customer) {
                return !empty($customer['ledger_account_id']);
            })
            ->values();

        $units = Unit::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function (Unit $unit) {
                return [
                    'id' => $unit->id,
                    'name' => $unit->name,
                    'symbol' => $unit->symbol,
                ];
            });

        $defaultExpiryDate = now()->addDays(30)->format('Y-m-d');

        return response()->json([
            'success' => true,
            'message' => 'Quotation form data retrieved successfully',
            'data' => [
                'customers' => $customers,
                'products' => $products,
                'units' => $units,
                'default_expiry_date' => $defaultExpiryDate,
            ],
        ]);
    }

    /**
     * Store a new quotation.
     */
    public function store(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), $this->rules());

        $validator->after(function ($validator) use ($request, $tenant) {
            $ledgerId = $request->get('customer_ledger_id');
            if ($ledgerId && !LedgerAccount::where('tenant_id', $tenant->id)->where('id', $ledgerId)->exists()) {
                $validator->errors()->add('customer_ledger_id', 'Selected customer ledger is invalid.');
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
                            ->orWhere('is_active', false)
                            ->orWhere('is_saleable', false);
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
            $lastQuotation = Quotation::where('tenant_id', $tenant->id)
                ->latest('id')
                ->first();
            $nextNumber = $lastQuotation ? $lastQuotation->quotation_number + 1 : 1;

            $customerLedger = LedgerAccount::where('tenant_id', $tenant->id)
                ->findOrFail($request->get('customer_ledger_id'));
            $customer = Customer::where('ledger_account_id', $customerLedger->id)->first();
            $vendor = Vendor::where('ledger_account_id', $customerLedger->id)->first();

            $quotation = Quotation::create([
                'tenant_id' => $tenant->id,
                'quotation_number' => $nextNumber,
                'quotation_date' => $request->get('quotation_date'),
                'expiry_date' => $request->get('expiry_date'),
                'customer_id' => $customer?->id,
                'vendor_id' => $vendor?->id,
                'customer_ledger_id' => $customerLedger->id,
                'reference_number' => $request->get('reference_number'),
                'subject' => $request->get('subject'),
                'terms_and_conditions' => $request->get('terms_and_conditions'),
                'notes' => $request->get('notes'),
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            foreach ($request->get('items', []) as $index => $item) {
                $product = Product::where('tenant_id', $tenant->id)->findOrFail($item['product_id']);

                $quotation->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'description' => $item['description'] ?? $product->description,
                    'quantity' => $item['quantity'],
                    'unit' => $product->primaryUnit->symbol ?? 'Pcs',
                    'rate' => $item['rate'],
                    'discount' => $item['discount'] ?? 0,
                    'tax' => $item['tax'] ?? 0,
                    'is_tax_inclusive' => $item['is_tax_inclusive'] ?? false,
                    'sort_order' => $index,
                ]);
            }

            $quotation->load('items');
            $quotation->calculateTotals();
            $quotation->save();

            if ($request->get('action') === 'save_and_send') {
                if (!$quotation->markAsSent()) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Quotation could not be marked as sent.',
                    ], 422);
                }
            }

            DB::commit();

            $quotation->load(['customer', 'vendor', 'customerLedger', 'items']);

            return response()->json([
                'success' => true,
                'message' => $request->get('action') === 'save_and_send'
                    ? 'Quotation created and marked as sent successfully'
                    : 'Quotation created successfully',
                'data' => [
                    'quotation' => $this->formatQuotation($quotation, true),
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating quotation: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the quotation.',
            ], 500);
        }
    }

    /**
     * Show a quotation.
     */
    public function show(Tenant $tenant, Quotation $quotation)
    {
        if ($quotation->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Quotation not found',
            ], 404);
        }

        $quotation->load(['customer', 'vendor', 'customerLedger', 'items.product', 'createdBy', 'updatedBy', 'convertedToInvoice.voucherType']);

        return response()->json([
            'success' => true,
            'message' => 'Quotation retrieved successfully',
            'data' => [
                'quotation' => $this->formatQuotation($quotation, true),
            ],
        ]);
    }

    /**
     * Update a quotation (draft only).
     */
    public function update(Request $request, Tenant $tenant, Quotation $quotation)
    {
        if ($quotation->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Quotation not found',
            ], 404);
        }

        if (!$quotation->canBeEdited()) {
            return response()->json([
                'success' => false,
                'message' => 'Only draft quotations can be edited.',
            ], 403);
        }

        $validator = Validator::make($request->all(), $this->rules());

        $validator->after(function ($validator) use ($request, $tenant) {
            $ledgerId = $request->get('customer_ledger_id');
            if ($ledgerId && !LedgerAccount::where('tenant_id', $tenant->id)->where('id', $ledgerId)->exists()) {
                $validator->errors()->add('customer_ledger_id', 'Selected customer ledger is invalid.');
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
                            ->orWhere('is_active', false)
                            ->orWhere('is_saleable', false);
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
            $customerLedger = LedgerAccount::where('tenant_id', $tenant->id)
                ->findOrFail($request->get('customer_ledger_id'));
            $customer = Customer::where('ledger_account_id', $customerLedger->id)->first();
            $vendor = Vendor::where('ledger_account_id', $customerLedger->id)->first();

            $quotation->update([
                'quotation_date' => $request->get('quotation_date'),
                'expiry_date' => $request->get('expiry_date'),
                'customer_id' => $customer?->id,
                'vendor_id' => $vendor?->id,
                'customer_ledger_id' => $customerLedger->id,
                'reference_number' => $request->get('reference_number'),
                'subject' => $request->get('subject'),
                'terms_and_conditions' => $request->get('terms_and_conditions'),
                'notes' => $request->get('notes'),
                'updated_by' => Auth::id(),
            ]);

            $quotation->items()->delete();

            foreach ($request->get('items', []) as $index => $item) {
                $product = Product::where('tenant_id', $tenant->id)->findOrFail($item['product_id']);

                $quotation->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'description' => $item['description'] ?? $product->description,
                    'quantity' => $item['quantity'],
                    'unit' => $product->primaryUnit->symbol ?? 'Pcs',
                    'rate' => $item['rate'],
                    'discount' => $item['discount'] ?? 0,
                    'tax' => $item['tax'] ?? 0,
                    'is_tax_inclusive' => $item['is_tax_inclusive'] ?? false,
                    'sort_order' => $index,
                ]);
            }

            $quotation->load('items');
            $quotation->calculateTotals();
            $quotation->save();

            DB::commit();

            $quotation->load(['customer', 'vendor', 'customerLedger', 'items']);

            return response()->json([
                'success' => true,
                'message' => 'Quotation updated successfully',
                'data' => [
                    'quotation' => $this->formatQuotation($quotation, true),
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating quotation: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the quotation.',
            ], 500);
        }
    }

    /**
     * Delete a quotation (draft only).
     */
    public function destroy(Tenant $tenant, Quotation $quotation)
    {
        if ($quotation->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Quotation not found',
            ], 404);
        }

        if (!$quotation->canBeDeleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Only draft quotations can be deleted.',
            ], 403);
        }

        DB::beginTransaction();
        try {
            $quotation->items()->delete();
            $quotation->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Quotation deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting quotation: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the quotation.',
            ], 500);
        }
    }

    /**
     * Duplicate quotation.
     */
    public function duplicate(Tenant $tenant, Quotation $quotation)
    {
        if ($quotation->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Quotation not found',
            ], 404);
        }

        DB::beginTransaction();
        try {
            $lastQuotation = Quotation::where('tenant_id', $tenant->id)
                ->latest('id')
                ->first();
            $nextNumber = $lastQuotation ? $lastQuotation->quotation_number + 1 : 1;

            $newQuotation = Quotation::create([
                'tenant_id' => $quotation->tenant_id,
                'quotation_number' => $nextNumber,
                'quotation_date' => now(),
                'expiry_date' => now()->addDays(30),
                'customer_id' => $quotation->customer_id,
                'vendor_id' => $quotation->vendor_id,
                'customer_ledger_id' => $quotation->customer_ledger_id,
                'reference_number' => null,
                'subject' => $quotation->subject,
                'terms_and_conditions' => $quotation->terms_and_conditions,
                'notes' => $quotation->notes,
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            foreach ($quotation->items as $item) {
                $newQuotation->items()->create([
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'rate' => $item->rate,
                    'discount' => $item->discount,
                    'tax' => $item->tax,
                    'is_tax_inclusive' => $item->is_tax_inclusive,
                    'sort_order' => $item->sort_order,
                ]);
            }

            $newQuotation->load('items');
            $newQuotation->calculateTotals();
            $newQuotation->save();

            DB::commit();

            $newQuotation->load(['customer', 'vendor', 'customerLedger', 'items']);

            return response()->json([
                'success' => true,
                'message' => 'Quotation duplicated successfully',
                'data' => [
                    'quotation' => $this->formatQuotation($newQuotation, true),
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error duplicating quotation: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while duplicating the quotation.',
            ], 500);
        }
    }

    /**
     * Mark quotation as sent.
     */
    public function send(Tenant $tenant, Quotation $quotation)
    {
        if ($quotation->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Quotation not found',
            ], 404);
        }

        if (!$quotation->canBeSent()) {
            return response()->json([
                'success' => false,
                'message' => 'This quotation cannot be marked as sent.',
            ], 422);
        }

        $quotation->markAsSent();

        return response()->json([
            'success' => true,
            'message' => 'Quotation marked as sent successfully',
            'data' => [
                'quotation' => $this->formatQuotation($quotation),
            ],
        ]);
    }

    /**
     * Mark quotation as accepted.
     */
    public function accept(Tenant $tenant, Quotation $quotation)
    {
        if ($quotation->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Quotation not found',
            ], 404);
        }

        if ($quotation->status !== 'sent') {
            return response()->json([
                'success' => false,
                'message' => 'Only sent quotations can be marked as accepted.',
            ], 422);
        }

        $quotation->markAsAccepted();

        return response()->json([
            'success' => true,
            'message' => 'Quotation marked as accepted successfully',
            'data' => [
                'quotation' => $this->formatQuotation($quotation),
            ],
        ]);
    }

    /**
     * Mark quotation as rejected.
     */
    public function reject(Request $request, Tenant $tenant, Quotation $quotation)
    {
        if ($quotation->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Quotation not found',
            ], 404);
        }

        if ($quotation->status !== 'sent') {
            return response()->json([
                'success' => false,
                'message' => 'Only sent quotations can be marked as rejected.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $quotation->markAsRejected($request->get('rejection_reason'));

        return response()->json([
            'success' => true,
            'message' => 'Quotation marked as rejected successfully',
            'data' => [
                'quotation' => $this->formatQuotation($quotation),
            ],
        ]);
    }

    /**
     * Convert quotation to invoice.
     */
    public function convert(Tenant $tenant, Quotation $quotation)
    {
        if ($quotation->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Quotation not found',
            ], 404);
        }

        if (!$quotation->canBeConverted()) {
            return response()->json([
                'success' => false,
                'message' => 'This quotation cannot be converted to an invoice.',
            ], 422);
        }

        try {
            $invoice = $quotation->convertToInvoice();

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to convert quotation to invoice.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Quotation converted to invoice successfully',
                'data' => [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->voucher_number,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error converting quotation to invoice: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while converting the quotation.',
            ], 500);
        }
    }

    /**
     * Download quotation as PDF.
     */
    public function pdf(Tenant $tenant, Quotation $quotation)
    {
        if ($quotation->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Quotation not found',
            ], 404);
        }

        try {
            $quotation->load(['customer', 'vendor', 'customerLedger', 'items.product', 'createdBy']);

            $pdf = Pdf::loadView('tenant.accounting.quotations.pdf', compact('tenant', 'quotation'));

            return $pdf->download('quotation-' . $quotation->getQuotationNumber() . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error generating quotation PDF: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate PDF.',
            ], 500);
        }
    }

    /**
     * Email quotation with PDF attachment.
     */
    public function email(Request $request, Tenant $tenant, Quotation $quotation)
    {
        if ($quotation->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Quotation not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'to' => 'required|email',
            'subject' => 'required|string|max:255',
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
            $quotation->load(['customer', 'vendor', 'customerLedger', 'items.product', 'createdBy']);
            $pdf = Pdf::loadView('tenant.accounting.quotations.pdf', compact('tenant', 'quotation'));

            Mail::send('emails.quotation', [
                'quotation' => $quotation,
                'tenant' => $tenant,
                'emailMessage' => $request->get('message'),
            ], function ($mail) use ($request, $quotation, $pdf) {
                $mail->to($request->get('to'))
                    ->subject($request->get('subject'))
                    ->attachData($pdf->output(), 'quotation-' . $quotation->getQuotationNumber() . '.pdf', [
                        'mime' => 'application/pdf',
                    ]);
            });

            if ($quotation->status === 'draft') {
                $quotation->markAsSent();
            }

            return response()->json([
                'success' => true,
                'message' => 'Quotation sent successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending quotation email: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to send email',
            ], 500);
        }
    }

    /**
     * Search customers for quotation.
     */
    public function searchCustomers(Request $request, Tenant $tenant)
    {
        $query = trim($request->get('q', ''));

        $customersQuery = Customer::where('tenant_id', $tenant->id)
            ->with('ledgerAccount');

        if (strlen($query) >= 2) {
            $customersQuery->where(function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                    ->orWhere('last_name', 'like', "%{$query}%")
                    ->orWhere('company_name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            });
        } else {
            $customersQuery->orderBy('updated_at', 'desc');
        }

        $customers = $customersQuery
            ->limit(10)
            ->get()
            ->map(function ($customer) {
                $ledger = $customer->ledgerAccount;

                return [
                    'id' => $customer->id,
                    'ledger_account_id' => $ledger?->id,
                    'ledger_account_name' => $ledger?->name,
                    'display_name' => $customer->company_name ?: trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')),
                    'email' => $customer->email,
                ];
            })
            ->filter(function ($customer) {
                return !empty($customer['ledger_account_id']);
            })
            ->values();

        return response()->json($customers);
    }

    /**
     * Search products for quotation items.
     */
    public function searchProducts(Request $request, Tenant $tenant)
    {
        $query = trim($request->get('q', ''));

        $productsQuery = Product::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->where('is_saleable', true)
            ->with(['primaryUnit']);

        if (strlen($query) >= 2) {
            $productsQuery->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            });
        } else {
            $productsQuery->orderBy('updated_at', 'desc');
        }

        $products = $productsQuery
            ->limit(15)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'sales_rate' => $product->sales_rate,
                    'purchase_rate' => $product->purchase_rate,
                    'current_stock' => $product->current_stock,
                    'unit' => $product->primaryUnit->symbol ?? 'Pcs',
                    'description' => $product->description,
                ];
            });

        return response()->json($products);
    }

    private function rules(): array
    {
        return [
            'quotation_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:quotation_date',
            'customer_ledger_id' => 'required|exists:ledger_accounts,id',
            'subject' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'terms_and_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.tax' => 'nullable|numeric|min:0',
            'items.*.description' => 'nullable|string',
            'items.*.is_tax_inclusive' => 'nullable|boolean',
        ];
    }

    private function formatQuotation(Quotation $quotation, bool $withItems = false): array
    {
        $customerName = $quotation->customer
            ? ($quotation->customer->company_name ?: trim(($quotation->customer->first_name ?? '') . ' ' . ($quotation->customer->last_name ?? '')))
            : null;

        $vendorName = $quotation->vendor
            ? ($quotation->vendor->company_name ?: trim(($quotation->vendor->first_name ?? '') . ' ' . ($quotation->vendor->last_name ?? '')))
            : null;

        $data = [
            'id' => $quotation->id,
            'quotation_number' => $quotation->quotation_number,
            'display_number' => $quotation->getQuotationNumber(),
            'quotation_date' => $quotation->quotation_date?->format('Y-m-d'),
            'expiry_date' => $quotation->expiry_date?->format('Y-m-d'),
            'customer_id' => $quotation->customer_id,
            'customer_name' => $customerName,
            'vendor_id' => $quotation->vendor_id,
            'vendor_name' => $vendorName,
            'customer_ledger_id' => $quotation->customer_ledger_id,
            'customer_ledger_name' => $quotation->customerLedger?->name,
            'reference_number' => $quotation->reference_number,
            'subject' => $quotation->subject,
            'terms_and_conditions' => $quotation->terms_and_conditions,
            'notes' => $quotation->notes,
            'subtotal' => (float) ($quotation->subtotal ?? 0),
            'discount_amount' => (float) ($quotation->discount_amount ?? 0),
            'tax_amount' => (float) ($quotation->tax_amount ?? 0),
            'total_discount' => (float) ($quotation->discount_amount ?? 0),
            'total_tax' => (float) ($quotation->tax_amount ?? 0),
            'total_amount' => (float) ($quotation->total_amount ?? 0),
            'status' => $quotation->status,
            'is_expired' => $quotation->isExpired(),
            'can_edit' => $quotation->canBeEdited(),
            'can_delete' => $quotation->canBeDeleted(),
            'can_send' => $quotation->canBeSent(),
            'can_convert' => $quotation->canBeConverted(),
            'sent_at' => $quotation->sent_at?->toDateTimeString(),
            'accepted_at' => $quotation->accepted_at?->toDateTimeString(),
            'rejected_at' => $quotation->rejected_at?->toDateTimeString(),
            'converted_at' => $quotation->converted_at?->toDateTimeString(),
            'rejection_reason' => $quotation->rejection_reason,
            'converted_to_invoice_id' => $quotation->converted_to_invoice_id,
            'created_at' => $quotation->created_at?->toDateTimeString(),
            'updated_at' => $quotation->updated_at?->toDateTimeString(),
        ];

        if ($withItems) {
            $data['items'] = $quotation->items->map(function (QuotationItem $item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'description' => $item->description,
                    'quantity' => (float) $item->quantity,
                    'unit' => $item->unit,
                    'rate' => (float) $item->rate,
                    'discount' => (float) $item->discount,
                    'tax' => (float) $item->tax,
                    'is_tax_inclusive' => (bool) $item->is_tax_inclusive,
                    'amount' => (float) $item->amount,
                    'total' => (float) $item->total,
                    'sort_order' => $item->sort_order,
                ];
            })->values();
        }

        return $data;
    }
}
