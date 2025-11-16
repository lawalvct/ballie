<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class VoucherEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'voucher_id',
        'ledger_account_id',
        'debit_amount',
        'credit_amount',
        'particulars',
        'document_path',
    ];

    protected $casts = [
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        // Update ledger account balance when voucher entry is created
        static::created(function ($voucherEntry) {
            // Only update if the voucher is posted
            if ($voucherEntry->voucher && $voucherEntry->voucher->status === 'posted') {
                $voucherEntry->updateLedgerAccountBalance();
            }
        });

        // Update ledger account balance when voucher entry is updated
        static::updated(function ($voucherEntry) {
            // Only update if the voucher is posted
            if ($voucherEntry->voucher && $voucherEntry->voucher->status === 'posted') {
                $voucherEntry->updateLedgerAccountBalance();

                // If ledger account changed, update the old account balance too
                if ($voucherEntry->isDirty('ledger_account_id')) {
                    $oldAccountId = $voucherEntry->getOriginal('ledger_account_id');
                    if ($oldAccountId) {
                        $oldAccount = LedgerAccount::find($oldAccountId);
                        if ($oldAccount) {
                            $oldAccount->updateCurrentBalance();
                        }
                    }
                }
            }
        });

        // Update ledger account balance when voucher entry is deleted
        static::deleted(function ($voucherEntry) {
            if ($voucherEntry->ledgerAccount) {
                $voucherEntry->updateLedgerAccountBalance();
            }
        });
    }
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
    // Relationships
    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function ledgerAccount()
    {
        return $this->belongsTo(LedgerAccount::class);
    }

    // Methods
    public function getAmount()
    {
        return $this->debit_amount > 0 ? $this->debit_amount : $this->credit_amount;
    }

    public function getType()
    {
        return $this->debit_amount > 0 ? 'dr' : 'cr';
    }

    public function isDebit()
    {
        return $this->debit_amount > 0;
    }

    public function isCredit()
    {
        return $this->credit_amount > 0;
    }
    public function account()
    {
        return $this->belongsTo(LedgerAccount::class, 'ledger_account_id');
    }
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Update the ledger account balance and customer outstanding balance
     */
    public function updateLedgerAccountBalance()
    {
        if ($this->ledgerAccount) {
            // Force recalculation without cache
            $currentBalance = $this->ledgerAccount->getCurrentBalance(null, false);

            // Update the ledger account current balance
            $this->ledgerAccount->update([
                'current_balance' => $currentBalance,
                'last_transaction_date' => $this->ledgerAccount->getLastTransactionDate()
            ]);

            // If this ledger account is linked to a customer, update customer balance
            $this->updateCustomerBalance($currentBalance);
        }
    }

    /**
     * Update customer outstanding balance if applicable
     */
    protected function updateCustomerBalance($ledgerBalance = null)
    {
        // Find customer linked to this ledger account
        $customer = Customer::where('ledger_account_id', $this->ledger_account_id)->first();

        if ($customer) {
            // Get the current balance from the ledger account
            if ($ledgerBalance === null) {
                $ledgerBalance = $this->ledgerAccount->getCurrentBalance(null, false);
            }

            // For customer accounts (typically asset accounts),
            // positive balance means customer owes money (outstanding balance)
            // negative balance means customer has credit
            $outstandingBalance = max(0, $ledgerBalance); // Only positive balances are outstanding

            // Update customer outstanding balance
            $customer->update(['outstanding_balance' => $outstandingBalance]);

            Log::info('VoucherEntry: Updated customer outstanding balance', [
                'customer_id' => $customer->id,
                'ledger_account_id' => $this->ledger_account_id,
                'ledger_balance' => $ledgerBalance,
                'outstanding_balance' => $outstandingBalance
            ]);
        }
    }

}
