<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BanksExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $banks;

    public function __construct($banks)
    {
        $this->banks = $banks;
    }

    public function collection()
    {
        return $this->banks;
    }

    public function headings(): array
    {
        return [
            'Bank Name',
            'Account Name',
            'Account Number',
            'Account Type',
            'Branch',
            'Current Balance',
            'Status',
            'Primary',
            'Payroll Account',
            'Last Reconciliation',
            'Opening Date',
        ];
    }

    public function map($bank): array
    {
        return [
            $bank->bank_name,
            $bank->account_name,
            $bank->account_number,
            $bank->account_type_display ?? 'N/A',
            $bank->branch_name ?? 'N/A',
            number_format($bank->getCurrentBalance(), 2),
            ucfirst($bank->status),
            $bank->is_primary ? 'Yes' : 'No',
            $bank->is_payroll_account ? 'Yes' : 'No',
            $bank->last_reconciliation_date ? $bank->last_reconciliation_date->format('Y-m-d') : 'Never',
            $bank->account_opening_date ? $bank->account_opening_date->format('Y-m-d') : 'N/A',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
