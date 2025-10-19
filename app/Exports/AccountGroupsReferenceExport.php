<?php

namespace App\Exports;

use App\Models\AccountGroup;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AccountGroupsReferenceExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $tenantId;

    public function __construct($tenantId)
    {
        $this->tenantId = $tenantId;
    }

    /**
     * Return account groups data
     */
    public function collection()
    {
        return AccountGroup::where('tenant_id', $this->tenantId)
            ->where('is_active', true)
            ->orderBy('nature')
            ->orderBy('name')
            ->get()
            ->map(function ($group) {
                return [
                    'name' => $group->name,
                    'code' => $group->code,
                    'nature' => ucfirst($group->nature),
                    'account_type' => $this->getNatureAccountType($group->nature),
                    'description' => $group->description ?? '',
                ];
            });
    }

    /**
     * Define column headings
     */
    public function headings(): array
    {
        return [
            'Account Group Name',
            'Group Code',
            'Nature',
            'Use for Account Type',
            'Description'
        ];
    }

    /**
     * Style the header row
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '3B82F6'], // Blue color
                ],
            ],
        ];
    }

    /**
     * Define column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 30,  // Account Group Name
            'B' => 15,  // Group Code
            'C' => 20,  // Nature
            'D' => 25,  // Use for Account Type
            'E' => 40,  // Description
        ];
    }

    /**
     * Get account type based on nature
     */
    private function getNatureAccountType($nature)
    {
        $mapping = [
            'assets' => 'asset',
            'liabilities' => 'liability',
            'equity' => 'equity',
            'income' => 'income',
            'expenses' => 'expense',
        ];

        return $mapping[strtolower($nature)] ?? $nature;
    }
}
