<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class CustomersTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * Return template array with sample data
     */
    public function array(): array
    {
        return [
            [
                'individual', // customer_type
                'John', // first_name
                'Doe', // last_name
                'ABC Company Ltd', // company_name
                'john.doe@example.com', // email
                '+234-800-123-4567', // phone
                '+234-800-123-4567', // mobile
                '123 Main Street', // address_line1
                'Suite 100', // address_line2
                'Lagos', // city
                'Lagos State', // state
                '100001', // postal_code
                'Nigeria', // country
                'NGN', // currency
                'Net 30', // payment_terms
                '12345678', // tax_id
                'Sample customer notes', // notes
                '5000.00', // opening_balance_amount (optional)
                'debit', // opening_balance_type (none/debit/credit)
                '2024-01-01', // opening_balance_date (YYYY-MM-DD)
            ],
            [
                'business', // customer_type
                '', // first_name (not required for business)
                '', // last_name (not required for business)
                'XYZ Trading Company', // company_name
                'contact@xyztrading.com', // email
                '+234-800-999-8888', // phone
                '+234-800-999-8888', // mobile
                '456 Market Road', // address_line1
                'Floor 5', // address_line2
                'Abuja', // city
                'FCT', // state
                '900001', // postal_code
                'Nigeria', // country
                'NGN', // currency
                'Net 60', // payment_terms
                '87654321', // tax_id
                'Business customer example', // notes
                '10000.00', // opening_balance_amount (optional)
                'credit', // opening_balance_type (none/debit/credit)
                '2024-01-01', // opening_balance_date (YYYY-MM-DD)
            ],
        ];
    }

    /**
     * Define column headings
     */
    public function headings(): array
    {
        return [
            'customer_type',
            'first_name',
            'last_name',
            'company_name',
            'email',
            'phone',
            'mobile',
            'address_line1',
            'address_line2',
            'city',
            'state',
            'postal_code',
            'country',
            'currency',
            'payment_terms',
            'tax_id',
            'notes',
            'opening_balance_amount',
            'opening_balance_type',
            'opening_balance_date',
        ];
    }

    /**
     * Apply styles to the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'], // Indigo-600
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
            'A' => 20, // customer_type
            'B' => 20, // first_name
            'C' => 20, // last_name
            'D' => 30, // company_name
            'E' => 30, // email
            'F' => 20, // phone
            'G' => 20, // mobile
            'H' => 35, // address_line1
            'I' => 25, // address_line2
            'J' => 20, // city
            'K' => 20, // state
            'L' => 15, // postal_code
            'M' => 15, // country
            'N' => 10, // currency
            'O' => 20, // payment_terms
            'P' => 20, // tax_id
            'Q' => 35, // notes
            'R' => 25, // opening_balance_amount
            'S' => 25, // opening_balance_type
            'T' => 25, // opening_balance_date
        ];
    }
}
