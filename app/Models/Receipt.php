<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToTenant;

class Receipt extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'sale_id',
        'receipt_number',
        'type',
        'receipt_data',
        'is_printed',
        'is_emailed',
        'printed_at',
        'emailed_at',
    ];

    protected $casts = [
        'receipt_data' => 'array',
        'is_printed' => 'boolean',
        'is_emailed' => 'boolean',
        'printed_at' => 'datetime',
        'emailed_at' => 'datetime',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    // Generate receipt number
    public static function generateReceiptNumber($sale): string
    {
        $prefix = 'REC-' . date('Y') . '-';
        $lastReceipt = static::where('receipt_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastReceipt) {
            $lastNumber = intval(substr($lastReceipt->receipt_number, strlen($prefix)));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    // Mark as printed
    public function markAsPrinted(): void
    {
        $this->update([
            'is_printed' => true,
            'printed_at' => now(),
        ]);
    }

    // Mark as emailed
    public function markAsEmailed(): void
    {
        $this->update([
            'is_emailed' => true,
            'emailed_at' => now(),
        ]);
    }
}
