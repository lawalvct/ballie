<?php

namespace App\Http\Controllers\Tenant\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Product;
use App\Models\LedgerAccount;
use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class QuotationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:accounting.quotations.manage')
            ->except(['index', 'show']);
        $this->middleware('permission:accounting.view')
            ->only(['index', 'show']);
    }

    public function index(Request $request, Tenant $tenant)
    {
        $query = Quotation::where('tenant_id', $tenant->id)
            ->with(['customer', 'vendor', 'customerLedger', 'createdBy', 'items']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('quotation_number', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('narration', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($subQ) use ($search) {
                      $subQ->where('company_name', 'like', "%{$search}%")
                           ->orWhere('first_name', 'like', "%{$search}%")
                           ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('quotation_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('quotation_date', '<=', $request->date_to);
        }

        // Filter by customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        $quotations = $query->latest('quotation_date')->paginate(15);

        // Get customers for filter dropdown
        $customers = Customer::where('tenant_id', $tenant->id)
            ->orderBy('company_name')
            ->orderBy('first_name')
            ->get();

        return view('tenant.accounting.quotations.index', compact('quotations', 'tenant', 'customers'));
    }

    public function create(Tenant $tenant)
    {
        // Get products
        $products = Product::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->where('is_saleable', true)
            ->with(['primaryUnit'])
            ->orderBy('name')
            ->get();

        // Get customers and vendors
        $customers = Customer::with('ledgerAccount')
            ->where('tenant_id', $tenant->id)
            ->orderBy('company_name')
            ->orderBy('first_name')
            ->get();

        $vendors = Vendor::with('ledgerAccount')
            ->where('tenant_id', $tenant->id)
            ->orderBy('company_name')
            ->orderBy('first_name')
            ->get();

        // Get units for quick add product
        $units = Unit::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Default expiry date (30 days from now)
        $defaultExpiryDate = now()->addDays(30)->format('Y-m-d');

        return view('tenant.accounting.quotations.create', compact(
            'tenant',
            'products',
            'customers',
            'vendors',
            'units',
            'defaultExpiryDate'
        ));
    }

    public function store(Request $request, Tenant $tenant)
    {
        Log::info('=== QUOTATION STORE STARTED ===', [
            'tenant_id' => $tenant->id,
            'user_id' => auth()->id(),
        ]);

        $validator = Validator::make($request->all(), [
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
        ], [
            'customer_ledger_id.required' => 'Please select a customer before saving the quotation.',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation Failed', ['errors' => $validator->errors()]);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Generate quotation number
            $lastQuotation = Quotation::where('tenant_id', $tenant->id)
                ->latest('id')
                ->first();

            $nextNumber = $lastQuotation ? $lastQuotation->quotation_number + 1 : 1;

            // Get customer from ledger account
            $customerLedger = LedgerAccount::findOrFail($request->customer_ledger_id);
            $customer = Customer::where('ledger_account_id', $customerLedger->id)->first();
            $vendor = Vendor::where('ledger_account_id', $customerLedger->id)->first();

            // Create quotation
            $quotation = Quotation::create([
                'tenant_id' => $tenant->id,
                'quotation_number' => $nextNumber,
                'quotation_date' => $request->quotation_date,
                'expiry_date' => $request->expiry_date,
                'customer_id' => $customer?->id,
                'vendor_id' => $vendor?->id,
                'customer_ledger_id' => $request->customer_ledger_id,
                'reference_number' => $request->reference_number,
                'subject' => $request->subject,
                'terms_and_conditions' => $request->terms_and_conditions,
                'notes' => $request->notes,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            Log::info('Quotation Created', ['quotation_id' => $quotation->id]);

            // Create quotation items
            foreach ($request->items as $index => $item) {
                $product = Product::findOrFail($item['product_id']);

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

            // Calculate totals
            $quotation->load('items');
            $quotation->calculateTotals();
            $quotation->save();

            DB::commit();

            Log::info('=== QUOTATION STORE COMPLETED ===', [
                'quotation_id' => $quotation->id,
                'quotation_number' => $quotation->getQuotationNumber(),
            ]);

            // Check if user wants to send immediately
            if ($request->action === 'save_and_send') {
                $quotation->markAsSent();
                $message = 'Quotation created and marked as sent successfully!';
            } else {
                $message = 'Quotation created successfully!';
            }

            return redirect()
                ->route('tenant.accounting.quotations.show', [
                    'tenant' => $tenant->slug,
                    'quotation' => $quotation->id
                ])
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('=== QUOTATION STORE FAILED ===', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while creating the quotation: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Tenant $tenant, Quotation $quotation)
    {
        // Ensure the quotation belongs to the tenant
        if ($quotation->tenant_id !== $tenant->id) {
            abort(404);
        }

        $quotation->load([
            'customer',
            'vendor',
            'customerLedger',
            'items.product',
            'createdBy',
            'updatedBy',
            'convertedToInvoice.voucherType'
        ]);

        return view('tenant.accounting.quotations.show', compact('tenant', 'quotation'));
    }

    public function edit(Tenant $tenant, Quotation $quotation)
    {
        // Ensure the quotation belongs to the tenant
        if ($quotation->tenant_id !== $tenant->id) {
            abort(404);
        }

        if (!$quotation->canBeEdited()) {
            return redirect()
                ->route('tenant.accounting.quotations.show', [
                    'tenant' => $tenant->slug,
                    'quotation' => $quotation->id
                ])
                ->with('error', 'Only draft quotations can be edited.');
        }

        $quotation->load(['items', 'customer', 'vendor', 'customerLedger']);

        // Get products
        $products = Product::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->where('is_saleable', true)
            ->with(['primaryUnit'])
            ->orderBy('name')
            ->get();

        // Get customers and vendors
        $customers = Customer::with('ledgerAccount')
            ->where('tenant_id', $tenant->id)
            ->orderBy('company_name')
            ->orderBy('first_name')
            ->get();

        $vendors = Vendor::with('ledgerAccount')
            ->where('tenant_id', $tenant->id)
            ->orderBy('company_name')
            ->orderBy('first_name')
            ->get();

        // Get units
        $units = Unit::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('tenant.accounting.quotations.edit', compact(
            'tenant',
            'quotation',
            'products',
            'customers',
            'vendors',
            'units'
        ));
    }

    public function update(Request $request, Tenant $tenant, Quotation $quotation)
    {
        // Ensure the quotation belongs to the tenant
        if ($quotation->tenant_id !== $tenant->id) {
            abort(404);
        }

        if (!$quotation->canBeEdited()) {
            return redirect()->back()
                ->with('error', 'Only draft quotations can be edited.');
        }

        $validator = Validator::make($request->all(), [
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
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Get customer from ledger account
            $customerLedger = LedgerAccount::findOrFail($request->customer_ledger_id);
            $customer = Customer::where('ledger_account_id', $customerLedger->id)->first();
            $vendor = Vendor::where('ledger_account_id', $customerLedger->id)->first();

            // Update quotation
            $quotation->update([
                'quotation_date' => $request->quotation_date,
                'expiry_date' => $request->expiry_date,
                'customer_id' => $customer?->id,
                'vendor_id' => $vendor?->id,
                'customer_ledger_id' => $request->customer_ledger_id,
                'reference_number' => $request->reference_number,
                'subject' => $request->subject,
                'terms_and_conditions' => $request->terms_and_conditions,
                'notes' => $request->notes,
                'updated_by' => auth()->id(),
            ]);

            // Delete old items and create new ones
            $quotation->items()->delete();

            foreach ($request->items as $index => $item) {
                $product = Product::findOrFail($item['product_id']);

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

            // Recalculate totals
            $quotation->load('items');
            $quotation->calculateTotals();
            $quotation->save();

            DB::commit();

            return redirect()
                ->route('tenant.accounting.quotations.show', [
                    'tenant' => $tenant->slug,
                    'quotation' => $quotation->id
                ])
                ->with('success', 'Quotation updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating quotation: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while updating the quotation.')
                ->withInput();
        }
    }

    public function destroy(Tenant $tenant, Quotation $quotation)
    {
        // Ensure the quotation belongs to the tenant
        if ($quotation->tenant_id !== $tenant->id) {
            abort(404);
        }

        if (!$quotation->canBeDeleted()) {
            return redirect()->back()
                ->with('error', 'Only draft quotations can be deleted.');
        }

        try {
            DB::beginTransaction();

            // Delete items
            $quotation->items()->delete();

            // Delete quotation
            $quotation->delete();

            DB::commit();

            return redirect()
                ->route('tenant.accounting.quotations.index', ['tenant' => $tenant->slug])
                ->with('success', 'Quotation deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting quotation: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while deleting the quotation.');
        }
    }

    public function duplicate(Tenant $tenant, Quotation $quotation)
    {
        // Ensure the quotation belongs to the tenant
        if ($quotation->tenant_id !== $tenant->id) {
            abort(404);
        }

        try {
            DB::beginTransaction();

            // Generate new quotation number
            $lastQuotation = Quotation::where('tenant_id', $tenant->id)
                ->latest('id')
                ->first();

            $nextNumber = $lastQuotation ? $lastQuotation->quotation_number + 1 : 1;

            // Create duplicate quotation
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
                'created_by' => auth()->id(),
            ]);

            // Duplicate items
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

            // Calculate totals
            $newQuotation->load('items');
            $newQuotation->calculateTotals();
            $newQuotation->save();

            DB::commit();

            return redirect()
                ->route('tenant.accounting.quotations.show', [
                    'tenant' => $tenant->slug,
                    'quotation' => $newQuotation->id
                ])
                ->with('success', 'Quotation duplicated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error duplicating quotation: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while duplicating the quotation.');
        }
    }

    public function convertToInvoice(Tenant $tenant, Quotation $quotation)
    {
        // Ensure the quotation belongs to the tenant
        if ($quotation->tenant_id !== $tenant->id) {
            abort(404);
        }

        if (!$quotation->canBeConverted()) {
            return redirect()->back()
                ->with('error', 'This quotation cannot be converted to an invoice. It may be expired, already converted, or not in the correct status.');
        }

        try {
            $invoice = $quotation->convertToInvoice();

            if (!$invoice) {
                return redirect()->back()
                    ->with('error', 'Failed to convert quotation to invoice.');
            }

            return redirect()
                ->route('tenant.accounting.invoices.show', [
                    'tenant' => $tenant->slug,
                    'invoice' => $invoice->id
                ])
                ->with('success', 'Quotation converted to invoice successfully!');

        } catch (\Exception $e) {
            Log::error('Error converting quotation to invoice: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while converting the quotation: ' . $e->getMessage());
        }
    }

    public function markAsSent(Tenant $tenant, Quotation $quotation)
    {
        // Ensure the quotation belongs to the tenant
        if ($quotation->tenant_id !== $tenant->id) {
            abort(404);
        }

        if (!$quotation->canBeSent()) {
            return redirect()->back()
                ->with('error', 'This quotation cannot be marked as sent.');
        }

        try {
            $quotation->markAsSent();

            return redirect()->back()
                ->with('success', 'Quotation marked as sent successfully!');

        } catch (\Exception $e) {
            Log::error('Error marking quotation as sent: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while updating the quotation status.');
        }
    }

    public function markAsAccepted(Tenant $tenant, Quotation $quotation)
    {
        // Ensure the quotation belongs to the tenant
        if ($quotation->tenant_id !== $tenant->id) {
            abort(404);
        }

        if ($quotation->status !== 'sent') {
            return redirect()->back()
                ->with('error', 'Only sent quotations can be marked as accepted.');
        }

        try {
            $quotation->markAsAccepted();

            return redirect()->back()
                ->with('success', 'Quotation marked as accepted successfully!');

        } catch (\Exception $e) {
            Log::error('Error marking quotation as accepted: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while updating the quotation status.');
        }
    }

    public function markAsRejected(Request $request, Tenant $tenant, Quotation $quotation)
    {
        // Ensure the quotation belongs to the tenant
        if ($quotation->tenant_id !== $tenant->id) {
            abort(404);
        }

        if ($quotation->status !== 'sent') {
            return redirect()->back()
                ->with('error', 'Only sent quotations can be marked as rejected.');
        }

        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $quotation->markAsRejected($request->rejection_reason);

            return redirect()->back()
                ->with('success', 'Quotation marked as rejected.');

        } catch (\Exception $e) {
            Log::error('Error marking quotation as rejected: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while updating the quotation status.');
        }
    }

    public function print(Tenant $tenant, Quotation $quotation)
    {
        // Ensure the quotation belongs to the tenant
        if ($quotation->tenant_id !== $tenant->id) {
            abort(404);
        }

        $quotation->load([
            'customer',
            'vendor',
            'customerLedger',
            'items.product',
            'createdBy'
        ]);

        return view('tenant.accounting.quotations.print', compact('tenant', 'quotation'));
    }

    public function pdf(Tenant $tenant, Quotation $quotation)
    {
        // Ensure the quotation belongs to the tenant
        if ($quotation->tenant_id !== $tenant->id) {
            abort(404);
        }

        $quotation->load([
            'customer',
            'vendor',
            'customerLedger',
            'items.product',
            'createdBy'
        ]);

        $pdf = Pdf::loadView('tenant.accounting.quotations.pdf', compact('tenant', 'quotation'));

        return $pdf->download('quotation-' . $quotation->getQuotationNumber() . '.pdf');
    }

    public function email(Request $request, Tenant $tenant, Quotation $quotation)
    {
        // Ensure the quotation belongs to the tenant
        if ($quotation->tenant_id !== $tenant->id) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'to' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $quotation->load([
                'customer',
                'vendor',
                'customerLedger',
                'items.product',
                'createdBy'
            ]);

            // Generate PDF
            $pdf = Pdf::loadView('tenant.accounting.quotations.pdf', compact('tenant', 'quotation'));

            // Send email with PDF attachment
            Mail::send('emails.quotation', [
                'quotation' => $quotation,
                'tenant' => $tenant,
                'emailMessage' => $request->message,
            ], function ($mail) use ($request, $quotation, $pdf) {
                $mail->to($request->to)
                     ->subject($request->subject)
                     ->attachData($pdf->output(), 'quotation-' . $quotation->getQuotationNumber() . '.pdf', [
                         'mime' => 'application/pdf',
                     ]);
            });

            // Mark as sent if it's still draft
            if ($quotation->status === 'draft') {
                $quotation->markAsSent();
            }

            return response()->json(['message' => 'Quotation sent successfully']);

        } catch (\Exception $e) {
            Log::error('Error sending quotation email: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to send email'], 500);
        }
    }

    public function searchCustomers(Request $request, Tenant $tenant)
    {
        $query = trim($request->get('q', ''));

        $customersQuery = Customer::where('tenant_id', $tenant->id)
            ->with('ledgerAccount');

        if (strlen($query) >= 2) {
            $customersQuery->where(function($q) use ($query) {
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
            ->map(function($customer) {
                $ledgerAccount = $customer->ledgerAccount;

                return [
                    'id' => $customer->id,
                    'ledger_account_id' => $ledgerAccount?->id,
                    'ledger_account_name' => $ledgerAccount?->name,
                    'display_name' => $customer->company_name ?: trim($customer->first_name . ' ' . $customer->last_name),
                    'email' => $customer->email
                ];
            })
            ->filter(function($customer) {
                return !empty($customer['ledger_account_id']);
            })
            ->values();

        return response()->json($customers);
    }

    public function searchProducts(Request $request, Tenant $tenant)
    {
        $query = trim($request->get('q', ''));

        $productsQuery = Product::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->where('is_saleable', true)
            ->with(['primaryUnit']);

        if (strlen($query) >= 2) {
            $productsQuery->where(function($q) use ($query) {
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
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'sales_rate' => $product->sales_rate,
                    'purchase_rate' => $product->purchase_rate,
                    'current_stock' => $product->current_stock,
                    'unit' => $product->primaryUnit->symbol ?? 'Pcs',
                    'description' => $product->description
                ];
            });

        return response()->json($products);
    }
}
