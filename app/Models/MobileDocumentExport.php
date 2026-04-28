<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class MobileDocumentExport extends Model
{
    use HasFactory;

    public const TYPE_INVOICE_PDF = 'invoice_pdf';
    public const TYPE_CUSTOMER_STATEMENT = 'customer_statement';

    public const STATUS_PENDING = 'pending';
    public const STATUS_READY = 'ready';
    public const STATUS_FAILED = 'failed';
    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'export_uuid',
        'tenant_id',
        'user_id',
        'mobile_device_id',
        'document_type',
        'source_table',
        'source_sync_uuid',
        'source_server_id',
        'customer_sync_uuid',
        'disk',
        'storage_path',
        'mime_type',
        'file_name',
        'file_size',
        'checksum',
        'period_from',
        'period_to',
        'status',
        'error_message',
        'generated_at',
        'expires_at',
        'downloaded_at',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'period_from' => 'date',
        'period_to' => 'date',
        'generated_at' => 'datetime',
        'expires_at' => 'datetime',
        'downloaded_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->export_uuid)) {
                $model->export_uuid = (string) Str::uuid();
            }
            if (empty($model->disk)) {
                $model->disk = config('mobile_sync.documents.disk', 'local');
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(MobileDevice::class, 'mobile_device_id');
    }
}
