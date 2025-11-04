<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

/**
 * HasPosting Trait
 *
 * For models that have a posting workflow (draft -> posted -> cancelled).
 *
 * Usage:
 * 1. Add the trait to your model: use HasPosting;
 * 2. Ensure your table has the following columns:
 *    - status (enum: 'draft', 'posted', 'cancelled')
 *    - posted_by (unsignedBigInteger, nullable)
 *    - posted_at (timestamp, nullable)
 *
 * Example:
 * class Voucher extends Model
 * {
 *     use HasAudit, HasPosting;
 * }
 */
trait HasPosting
{
    /**
     * Post this record.
     *
     * @return bool
     */
    public function post(): bool
    {
        if ($this->status !== 'draft') {
            return false;
        }

        $this->status = 'posted';
        $this->posted_by = Auth::id();
        $this->posted_at = now();

        return $this->save();
    }

    /**
     * Unpost this record (revert to draft).
     *
     * @return bool
     */
    public function unpost(): bool
    {
        if ($this->status !== 'posted') {
            return false;
        }

        $this->status = 'draft';
        $this->posted_by = null;
        $this->posted_at = null;

        return $this->save();
    }

    /**
     * Cancel this record.
     *
     * @return bool
     */
    public function cancel(): bool
    {
        if ($this->status === 'cancelled') {
            return false;
        }

        $this->status = 'cancelled';

        return $this->save();
    }

    /**
     * Get the user who posted this record.
     */
    public function poster()
    {
        return $this->belongsTo(config('auth.providers.users.model', \App\Models\User::class), 'posted_by');
    }

    /**
     * Check if this record is posted.
     */
    public function isPosted(): bool
    {
        return $this->status === 'posted';
    }

    /**
     * Check if this record is draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if this record is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Scope to get only posted records.
     */
    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }

    /**
     * Scope to get only draft records.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope to get only cancelled records.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope to filter by poster.
     */
    public function scopePostedBy($query, $userId)
    {
        return $query->where('posted_by', $userId);
    }

    /**
     * Check if the current user posted this record.
     */
    public function wasPostedByCurrentUser(): bool
    {
        return Auth::check() && $this->posted_by === Auth::id();
    }
}
