<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasAudit;

class PurchaseOrder extends Model
{
    use SoftDeletes, HasAudit;

    protected $fillable = [
        'tenant_id',
        'vendor_id',
        'lpo_number',
        'lpo_date',
        'expected_delivery_date',
        'status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'notes',
        'terms_conditions',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'lpo_date' => 'date',
        'expected_delivery_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function generateLpoNumber($tenantId)
    {
        $lastLpo = self::where('tenant_id', $tenantId)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastLpo ? intval(substr($lastLpo->lpo_number, 4)) + 1 : 1;
        return 'LPO-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}
