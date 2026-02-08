<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomPlanInquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_name',
        'contact_name',
        'email',
        'phone',
        'num_companies',
        'interest',
        'requirements',
        'status',
        'admin_notes',
        'ip_address',
    ];

    protected $casts = [
        'num_companies' => 'integer',
    ];

    public function getInterestLabelAttribute(): string
    {
        return match($this->interest) {
            'lifetime' => 'Lifetime License',
            'custom_app' => 'Custom Branded App',
            'both' => 'Lifetime + Custom App',
            'other' => 'Other / Not Sure',
            default => $this->interest ?? 'Not specified',
        };
    }
}
