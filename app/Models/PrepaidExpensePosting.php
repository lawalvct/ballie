<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrepaidExpensePosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'prepaid_expense_id',
        'voucher_id',
        'installment_number',
        'amount',
        'posting_date',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'posting_date' => 'date',
    ];

    public function prepaidExpense()
    {
        return $this->belongsTo(PrepaidExpense::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
}
