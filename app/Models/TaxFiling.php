<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxFiling extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'type',
        'reference_number',
        'period_label',
        'period_start',
        'period_end',
        'amount',
        'status',
        'due_date',
        'filed_date',
        'paid_date',
        'payment_reference',
        'notes',
        'filed_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'period_start' => 'date',
        'period_end' => 'date',
        'due_date' => 'date',
        'filed_date' => 'date',
        'paid_date' => 'date',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function filer()
    {
        return $this->belongsTo(User::class, 'filed_by');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'paid')
            ->where('due_date', '<', now());
    }
}
