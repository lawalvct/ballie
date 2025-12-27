<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Backup extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'filename',
        'file_path',
        'file_size',
        'status',
        'error_message',
        'databases_count',
        'metadata',
        'completed_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the last successful backup
     */
    public static function getLastSuccessfulBackup()
    {
        return self::where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->first();
    }

    /**
     * Check if backup is overdue (more than 3 days)
     */
    public static function isBackupOverdue(): bool
    {
        $lastBackup = self::getLastSuccessfulBackup();

        if (!$lastBackup) {
            return true; // No backup exists
        }

        return $lastBackup->completed_at->diffInDays(now()) > 3;
    }

    /**
     * Get days since last backup
     */
    public static function daysSinceLastBackup(): ?int
    {
        $lastBackup = self::getLastSuccessfulBackup();

        if (!$lastBackup) {
            return null;
        }

        return $lastBackup->completed_at->diffInDays(now());
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return 'N/A';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = $this->file_size;
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
