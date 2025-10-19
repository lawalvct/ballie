<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VendorsTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * Return sample data for the template
     */
    public function array(): array
    {
        return [
            [
                'individual',
                'John',
                'Doe',
                '',
                'john.doe@example.com',
                '+234-801-234-5678',
                '+234-802-234-5678',
                'https://johndoe.com',
                'TIN-123456789',
                'RC-987654',
                '123 Main Street',
                'Suite 100',
                'Lagos',
                'Lagos State',
                '100001',
                'Nigeria',
                'NGN',
                'Net 30',
                'First Bank',
                '1234567890',
                'John Doe',
                'Preferred vendor for office supplies',
                1000.00,
                'credit',
                '2024-01-01'
            ],
            [
                'business',
                '',
                '',
                'ABC Supplies Ltd',
                'contact@abcsupplies.com',
                '+234-803-345-6789',
                '',
                'https://abcsupplies.com',
                'TIN-987654321',
                'RC-123456',
                '456 Business Avenue',
                'Floor 3',
                'Abuja',
                'FCT',
                '900001',
                'Nigeria',
                'NGN',
                'Net 45',
                'Access Bank',
                '0987654321',
                'ABC Supplies Ltd',
                'Main supplier for raw materials',
                5000.00,
                'credit',
                '2024-01-01'
            ],
        ];
    }

    /**
     * Define column headings
     */
    public function headings(): array
    {
        return [
            'vendor_type',
            'first_name',
            'last_name',
            'company_name',
            'email',
            'phone',
            'mobile',
            'website',
            'tax_id',
            'registration_number',
            'address_line1',
            'address_line2',
            'city',
            'state',
            'postal_code',
            'country',
            'currency',
            'payment_terms',
            'bank_name',
            'bank_account_number',
            'bank_account_name',
            'notes',
            'opening_balance_amount',
            'opening_balance_type',
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
                    'startColor' => ['rgb' => '7C3AED'], // Purple color for vendors
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
            'A' => 15,  // vendor_type
            'B' => 20,  // first_name
            'C' => 20,  // last_name
            'D' => 25,  // company_name
            'E' => 30,  // email
            'F' => 20,  // phone
            'G' => 20,  // mobile
            'H' => 25,  // website
            'I' => 20,  // tax_id
            'J' => 20,  // registration_number
            'K' => 30,  // address_line1
            'L' => 30,  // address_line2
            'M' => 20,  // city
            'N' => 20,  // state
            'O' => 15,  // postal_code
            'P' => 15,  // country
            'Q' => 10,  // currency
            'R' => 15,  // payment_terms
            'S' => 20,  // bank_name
            'T' => 20,  // bank_account_number
            'U' => 25,  // bank_account_name
            'V' => 30,  // notes
            'W' => 20,  // opening_balance_amount
            'X' => 20,  // opening_balance_type
            'Y' => 20,  // opening_balance_date
        ];
    }
}
