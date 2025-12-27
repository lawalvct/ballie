<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SupportTicketAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'reply_id',
        'filename',
        'original_name',
        'file_path',
        'file_size',
        'mime_type',
        'uploaded_by_type',
        'uploaded_by_id',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    /**
     * Get the ticket this attachment belongs to.
     */
    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    /**
     * Get the reply this attachment belongs to (if any).
     */
    public function reply()
    {
        return $this->belongsTo(SupportTicketReply::class, 'reply_id');
    }

    /**
     * Get the uploader (polymorphic).
     */
    public function uploader()
    {
        return $this->morphTo('uploaded_by', 'uploaded_by_type', 'uploaded_by_id');
    }

    /**
     * Get file size in human readable format.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get file icon based on mime type.
     */
    public function getFileIconAttribute(): string
    {
        if (str_starts_with($this->mime_type, 'image/')) {
            return 'photo';
        }

        return match($this->mime_type) {
            'application/pdf' => 'document-text',
            'application/zip', 'application/x-zip-compressed' => 'archive',
            'text/plain' => 'document',
            default => 'document',
        };
    }

    /**
     * Check if file is an image.
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Get full storage path.
     */
    public function getFullPathAttribute(): string
    {
        return Storage::path($this->file_path);
    }

    /**
     * Get download URL.
     */
    public function getDownloadUrlAttribute(): string
    {
        return route('tenant.support.attachments.download', [
            'tenant' => tenant()->slug,
            'attachment' => $this->id
        ]);
    }

    /**
     * Delete the physical file when model is deleted.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($attachment) {
            if (Storage::exists($attachment->file_path)) {
                Storage::delete($attachment->file_path);
            }
        });
    }
}
