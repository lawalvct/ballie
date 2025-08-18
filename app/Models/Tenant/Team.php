<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'tenant_id',
        'is_active',
        'color',
        'lead_user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($team) {
            if (empty($team->slug)) {
                $team->slug = \Illuminate\Support\Str::slug($team->name);
            }
            if (empty($team->tenant_id)) {
                $team->tenant_id = tenant('id');
            }
        });
    }

    /**
     * Get the users that belong to this team.
     */
    public function members()
    {
        return $this->belongsToMany(\App\Models\User::class, 'team_user', 'team_id', 'user_id')
                    ->withTimestamps();
    }

    /**
     * Get the team leader.
     */
    public function leader()
    {
        return $this->belongsTo(\App\Models\User::class, 'lead_user_id');
    }

    /**
     * Get the tenant that owns this team.
     */
    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }

    /**
     * Scope to active teams.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to tenant teams.
     */
    public function scopeForTenant($query, $tenantId = null)
    {
        $tenantId = $tenantId ?: tenant('id');
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Get formatted color.
     */
    public function getColorAttribute($value)
    {
        return $value ?: '#6366f1'; // Default purple color
    }

    /**
     * Get members count.
     */
    public function getMembersCountAttribute()
    {
        return $this->members()->count();
    }
}
