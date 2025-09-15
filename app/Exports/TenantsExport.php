<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TenantsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $tenants;

    public function __construct($tenants)
    {
        $this->tenants = $tenants;
    }

    public function collection()
    {
        return $this->tenants;
    }

    public function headings(): array
    {
        return [
            'Company Name',
            'Email',
            'Phone',
            'Business Type',
            'Slug',
            'Status',
            'Plan',
            'Billing Cycle',
            'Total Users',
            'Active Users',
            'Owner Name',
            'Owner Email',
            'Trial Ends At',
            'Created At',
            'Last Updated',
            'Created By'
        ];
    }

    public function map($tenant): array
    {
        $owner = $tenant->users->where('role', 'owner')->first();
        
        return [
            $tenant->name,
            $tenant->email,
            $tenant->phone ?: 'N/A',
            ucfirst(str_replace('_', ' ', $tenant->business_type)),
            $tenant->slug,
            ucfirst($tenant->subscription_status),
            $tenant->plan ? $tenant->plan->name : 'No Plan',
            ucfirst($tenant->billing_cycle ?: 'monthly'),
            $tenant->users->count(),
            $tenant->users->where('is_active', true)->count(),
            $owner ? $owner->name : 'N/A',
            $owner ? $owner->email : 'N/A',
            $tenant->trial_ends_at ? $tenant->trial_ends_at->format('Y-m-d H:i:s') : 'N/A',
            $tenant->created_at->format('Y-m-d H:i:s'),
            $tenant->updated_at->format('Y-m-d H:i:s'),
            $tenant->superAdmin ? $tenant->superAdmin->name : 'System'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}