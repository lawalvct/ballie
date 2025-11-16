<?php

namespace App\Exports;

use App\Models\LedgerAccount;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentEntriesTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    protected $tenantId;

    public function __construct($tenantId)
    {
        $this->tenantId = $tenantId;
    }

    /**
    * @return array
    */
    public function array(): array
    {
        // Get common expense ledger accounts as examples
        $expenseAccounts = LedgerAccount::where('tenant_id', $this->tenantId)
            ->where('is_active', true)
            ->whereIn('account_type', ['expense', 'asset'])
            ->orderBy('name')
            ->take(5)
            ->pluck('name')
            ->toArray();

        // Sample data rows
        return [
            [
                date('d-m-y'),
                $expenseAccounts[0] ?? 'Electricity Expense',
                'Payment for November electricity bill',
                '25000'
            ],
            [
                date('d-m-y'),
                $expenseAccounts[1] ?? 'Transportation',
                'Staff transport allowance',
                '15000'
            ],
            [
                date('d-m-y'),
                $expenseAccounts[2] ?? 'Office Supplies',
                'Purchase of stationery',
                '8500'
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'date',
            'ledger',
            'description',
            'amount'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E2EFDA']]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 30,
            'C' => 40,
            'D' => 15,
        ];
    }
}
