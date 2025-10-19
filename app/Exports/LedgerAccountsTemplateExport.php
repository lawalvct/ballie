<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LedgerAccountsTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * Return sample data for the template
     */
    public function array(): array
    {
        return [
            [
                'CASH-001',
                'Petty Cash',
                'asset',
                'Cash & Bank',
                '',
                'dr',
                5000.00,
                'Petty cash for small expenses',
                'Main Office',
                '+234-801-234-5678',
                'accounts@example.com',
                'yes',
                '2024-01-01'
            ],
            [
                'BANK-001',
                'First Bank - Current Account',
                'asset',
                'Cash & Bank',
                '',
                'dr',
                100000.00,
                'Main operating bank account',
                '',
                '',
                '',
                'yes',
                '2024-01-01'
            ],
            [
                'EXP-001',
                'Office Rent',
                'expense',
                'Operating Expenses',
                '',
                'dr',
                0.00,
                'Monthly office rent expense',
                '',
                '',
                '',
                'yes',
                ''
            ],
        ];
    }

    /**
     * Define column headings
     */
    public function headings(): array
    {
        return [
            'code',
            'name',
            'account_type',
            'account_group',
            'parent_code',
            'balance_type',
            'opening_balance',
            'description',
            'address',
            'phone',
            'email',
            'is_active',
            'opening_balance_date'
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
                    'startColor' => ['rgb' => '3B82F6'], // Blue color for ledger accounts
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
            'A' => 15,  // code
            'B' => 35,  // name
            'C' => 15,  // account_type
            'D' => 25,  // account_group
            'E' => 15,  // parent_code
            'F' => 15,  // balance_type
            'G' => 18,  // opening_balance
            'H' => 40,  // description
            'I' => 30,  // address
            'J' => 20,  // phone
            'K' => 30,  // email
            'L' => 12,  // is_active
            'M' => 20,  // opening_balance_date
        ];
    }
}
