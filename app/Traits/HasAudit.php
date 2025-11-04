<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

/**
 * HasAudit Trait
 *
 * Automatically tracks user actions on model records.
 *
 * Usage:
 * 1. Add the trait to your model: use HasAudit;
 * 2. Ensure your table has the following columns:
 *    - created_by (unsignedBigInteger, nullable)
 *    - updated_by (unsignedBigInteger, nullable)
 *    - deleted_by (unsignedBigInteger, nullable) - for soft deletes
 *
 * The trait will automatically:
 * - Set created_by when a new record is created
 * - Update updated_by when a record is modified
 * - Set deleted_by when a record is soft deleted
 *
 * Example:
 * class Customer extends Model
 * {
 *     use HasAudit, SoftDeletes;
 * }
 */
trait HasAudit
{
    /**
     * Boot the trait.
     * Register model event listeners.
     */
    protected static function bootHasAudit(): void
    {
        // Set created_by when creating a new record
        static::creating(function ($model) {
            if (Auth::check() && !$model->created_by) {
                $model->created_by = Auth::id();
            }
        });

        // Set updated_by when updating a record
        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });

        // Set deleted_by when soft deleting a record (if SoftDeletes trait is used)
        static::deleting(function ($model) {
            if (Auth::check() && method_exists($model, 'isForceDeleting') && !$model->isForceDeleting()) {
                // Only set deleted_by for soft deletes, not force deletes
                if (in_array('deleted_by', $model->getFillable()) || $model->isFillable('deleted_by')) {
                    $model->deleted_by = Auth::id();
                    $model->save();
                }
            }
        });
    }

    /**
     * Get the user who created this record.
     */
    public function creator()
    {
        return $this->belongsTo(config('auth.providers.users.model', \App\Models\User::class), 'created_by');
    }

    /**
     * Get the user who last updated this record.
     */
    public function updater()
    {
        return $this->belongsTo(config('auth.providers.users.model', \App\Models\User::class), 'updated_by');
    }

    /**
     * Get the user who deleted this record.
     */
    public function deleter()
    {
        return $this->belongsTo(config('auth.providers.users.model', \App\Models\User::class), 'deleted_by');
    }

    /**
     * Scope to filter by creator.
     */
    public function scopeCreatedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    /**
     * Scope to filter by updater.
     */
    public function scopeUpdatedBy($query, $userId)
    {
        return $query->where('updated_by', $userId);
    }

    /**
     * Check if the current user created this record.
     */
    public function wasCreatedByCurrentUser(): bool
    {
        return Auth::check() && $this->created_by === Auth::id();
    }

    /**
     * Check if the current user last updated this record.
     */
    public function wasUpdatedByCurrentUser(): bool
    {
        return Auth::check() && $this->updated_by === Auth::id();
    }
}
