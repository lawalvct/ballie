<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Quotation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'quotation_number',
        'quotation_date',
        'expiry_date',
        'customer_id',
        'vendor_id',
        'customer_ledger_id',
        'reference_number',
        'subject',
        'terms_and_conditions',
        'notes',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'status',
        'converted_to_invoice_id',
        'converted_at',
        'sent_at',
        'accepted_at',
        'rejected_at',
        'rejection_reason',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'expiry_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'converted_at' => 'datetime',
        'sent_at' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function customerLedger(): BelongsTo
    {
        return $this->belongsTo(LedgerAccount::class, 'customer_ledger_id');
    }

    public function convertedToInvoice(): BelongsTo
    {
        return $this->belongsTo(Voucher::class, 'converted_to_invoice_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class)->orderBy('sort_order');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['expired', 'converted']);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
            ->orWhere(function($q) {
                $q->where('expiry_date', '<', now())
                  ->whereNotIn('status', ['converted', 'expired']);
            });
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Methods
     */
    public function getQuotationNumber(): string
    {
        return 'QT-' . str_pad($this->quotation_number, 4, '0', STR_PAD_LEFT);
    }

    public function getDisplayNumber(): string
    {
        return $this->getQuotationNumber();
    }

    public function isExpired(): bool
    {
        if ($this->status === 'expired') {
            return true;
        }

        if ($this->expiry_date && $this->expiry_date->isPast()) {
            return true;
        }

        return false;
    }

    public function canBeEdited(): bool
    {
        return $this->status === 'draft';
    }

    public function canBeDeleted(): bool
    {
        return $this->status === 'draft';
    }

    public function canBeConverted(): bool
    {
        // Can convert if sent or accepted, not expired, and not already converted
        if ($this->status === 'converted') {
            return false;
        }

        if ($this->isExpired()) {
            return false;
        }

        return in_array($this->status, ['sent', 'accepted']);
    }

    public function canBeSent(): bool
    {
        return $this->status === 'draft' && $this->items()->count() > 0;
    }

    public function markAsSent(): bool
    {
        if (!$this->canBeSent()) {
            return false;
        }

        return $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function markAsAccepted(): bool
    {
        if ($this->status !== 'sent') {
            return false;
        }

        return $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);
    }

    public function markAsRejected(string $reason = null): bool
    {
        if ($this->status !== 'sent') {
            return false;
        }

        return $this->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function markAsExpired(): bool
    {
        if ($this->status === 'converted') {
            return false;
        }

        return $this->update([
            'status' => 'expired',
        ]);
    }

    public function calculateTotals(): void
    {
        $subtotal = 0;
        $taxAmount = 0;
        $discountAmount = 0;

        foreach ($this->items as $item) {
            $subtotal += $item->amount;
            $taxAmount += $item->tax;
            $discountAmount += $item->discount;
        }

        $this->subtotal = $subtotal;
        $this->tax_amount = $taxAmount;
        $this->discount_amount = $discountAmount;
        $this->total_amount = $subtotal + $taxAmount - $discountAmount;
    }

    public function convertToInvoice(): ?Voucher
    {
        if (!$this->canBeConverted()) {
            Log::warning('Quotation cannot be converted', [
                'quotation_id' => $this->id,
                'status' => $this->status,
                'is_expired' => $this->isExpired(),
            ]);
            return null;
        }

        try {
            DB::beginTransaction();

            // Get sales voucher type
            $voucherType = VoucherType::where('tenant_id', $this->tenant_id)
                ->where('code', 'SV')
                ->first();

            if (!$voucherType) {
                throw new \Exception('Sales voucher type not found');
            }

            // Generate voucher number
            $lastVoucher = Voucher::where('tenant_id', $this->tenant_id)
                ->where('voucher_type_id', $voucherType->id)
                ->latest('id')
                ->first();

            $nextNumber = 1;
            if ($lastVoucher) {
                $rawNumber = $lastVoucher->voucher_number;
                if (is_numeric($rawNumber)) {
                    $nextNumber = (int) $rawNumber + 1;
                } elseif (preg_match('/(\d+)(?!.*\d)/', (string) $rawNumber, $matches)) {
                    $nextNumber = (int) $matches[1] + 1;
                } else {
                    $nextNumber = $lastVoucher->id + 1;
                }
            }

            // Prepare inventory items from quotation items
            $inventoryItems = [];
            foreach ($this->items as $item) {
                $inventoryItems[] = [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'rate' => $item->rate,
                    'amount' => $item->amount,
                    'purchase_rate' => $item->product->purchase_rate ?? 0,
                    'discount' => $item->discount,
                    'tax' => $item->tax,
                    'is_tax_inclusive' => $item->is_tax_inclusive,
                    'total' => $item->total,
                ];
            }

            // Create voucher
            $voucher = Voucher::create([
                'tenant_id' => $this->tenant_id,
                'voucher_type_id' => $voucherType->id,
                'voucher_number' => $nextNumber,
                'voucher_date' => now(),
                'reference_number' => 'QT-' . $this->quotation_number,
                'narration' => 'Converted from Quotation ' . $this->getQuotationNumber() .
                              ($this->subject ? ' - ' . $this->subject : ''),
                'total_amount' => $this->total_amount,
                'status' => 'posted',
                'created_by' => auth()->id(),
                'posted_at' => now(),
                'posted_by' => auth()->id(),
                'quotation_id' => $this->id,
                'meta_data' => json_encode(['inventory_items' => $inventoryItems]),
            ]);

            // Create invoice items
            foreach ($inventoryItems as $item) {
                $voucher->items()->create([
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'rate' => $item['rate'],
                    'amount' => $item['amount'],
                    'purchase_rate' => $item['purchase_rate'],
                    'discount' => $item['discount'],
                    'tax' => $item['tax'],
                    'is_tax_inclusive' => $item['is_tax_inclusive'],
                    'total' => $item['total'],
                ]);
            }

            // Create accounting entries (simplified - you may need to adjust based on your InvoiceController logic)
            $this->createAccountingEntriesForInvoice($voucher, $inventoryItems);

            // Update product stock
            $this->updateProductStockForInvoice($inventoryItems, $voucher);

            // Update quotation status
            $this->update([
                'status' => 'converted',
                'converted_to_invoice_id' => $voucher->id,
                'converted_at' => now(),
            ]);

            DB::commit();

            Log::info('Quotation converted to invoice successfully', [
                'quotation_id' => $this->id,
                'voucher_id' => $voucher->id,
            ]);

            return $voucher;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error converting quotation to invoice: ' . $e->getMessage(), [
                'quotation_id' => $this->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    private function createAccountingEntriesForInvoice(Voucher $voucher, array $inventoryItems): void
    {
        // Get customer account
        $customerAccount = $this->customerLedger;

        // Calculate total
        $totalAmount = collect($inventoryItems)->sum('amount');

        // Debit: Customer Account (Accounts Receivable)
        VoucherEntry::create([
            'voucher_id' => $voucher->id,
            'ledger_account_id' => $customerAccount->id,
            'debit_amount' => $totalAmount,
            'credit_amount' => 0,
            'particulars' => 'Sales invoice from quotation - ' . $voucher->voucher_number,
        ]);

        // Group items by sales account
        $groupedItems = [];
        foreach ($inventoryItems as $item) {
            $product = Product::find($item['product_id']);
            $accountId = $product->sales_account_id;

            if (!$accountId) {
                $defaultAccount = LedgerAccount::where('tenant_id', $this->tenant_id)
                    ->where('name', 'Sales Revenue')
                    ->first();
                $accountId = $defaultAccount->id;
            }

            if (!isset($groupedItems[$accountId])) {
                $groupedItems[$accountId] = 0;
            }
            $groupedItems[$accountId] += $item['amount'];
        }

        // Credit: Sales Account(s)
        foreach ($groupedItems as $accountId => $amount) {
            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $accountId,
                'debit_amount' => 0,
                'credit_amount' => $amount,
                'particulars' => 'Sales from quotation - ' . $voucher->voucher_number,
            ]);
        }

        // Update ledger balances
        $customerAccount->updateCurrentBalance();
        foreach ($groupedItems as $accountId => $amount) {
            LedgerAccount::find($accountId)->updateCurrentBalance();
        }
    }

    private function updateProductStockForInvoice(array $inventoryItems, Voucher $voucher): void
    {
        foreach ($inventoryItems as $item) {
            $product = Product::find($item['product_id']);
            if ($product && $product->maintain_stock) {
                StockMovement::createFromVoucher($voucher, $item, 'out');
            }
        }
    }

    /**
     * Get status badge color
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'sent' => 'blue',
            'accepted' => 'green',
            'rejected' => 'red',
            'expired' => 'yellow',
            'converted' => 'purple',
            default => 'gray',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string
    {
        return ucfirst($this->status);
    }
}
