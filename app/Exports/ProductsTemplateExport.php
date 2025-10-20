<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class ProductsTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    public function collection()
    {
        // Return empty collection with sample row
        return collect([
            [
                'Sample Product',
                'item',
                'SP-001',
                'This is a sample product description',
                'Electronics', // Category name
                'Samsung',
                '1234567890',
                1000.00,
                1500.00,
                1500.00,
                'Piece', // Unit name
                1,
                100, // Opening stock
                '2024-01-01', // Opening stock date
                50, // Reorder level
                'Stock Asset', // Stock asset account name
                'Sales Income', // Sales account name
                'Purchase Expense', // Purchase account name
                5.00, // Tax rate
                'no', // Tax inclusive
                'BARCODE123',
                'yes', // Maintain stock
                'yes', // Is active
                'yes', // Is saleable
                'yes', // Is purchasable
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'Product Name*',
            'Type* (item/service)',
            'SKU',
            'Description',
            'Category',
            'Brand',
            'HSN Code',
            'Purchase Rate*',
            'Sales Rate*',
            'MRP',
            'Primary Unit*',
            'Unit Conversion Factor',
            'Opening Stock',
            'Opening Stock Date',
            'Reorder Level',
            'Stock Asset Account',
            'Sales Account',
            'Purchase Account',
            'Tax Rate (%)',
            'Tax Inclusive (yes/no)',
            'Barcode',
            'Maintain Stock (yes/no)',
            'Is Active (yes/no)',
            'Is Saleable (yes/no)',
            'Is Purchasable (yes/no)',
        ];
    }

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
                    'startColor' => ['rgb' => '3B82F6'],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25, // Product Name
            'B' => 15, // Type
            'C' => 15, // SKU
            'D' => 30, // Description
            'E' => 20, // Category
            'F' => 15, // Brand
            'G' => 15, // HSN Code
            'H' => 15, // Purchase Rate
            'I' => 15, // Sales Rate
            'J' => 15, // MRP
            'K' => 15, // Primary Unit
            'L' => 20, // Unit Conversion Factor
            'M' => 15, // Opening Stock
            'N' => 18, // Opening Stock Date
            'O' => 15, // Reorder Level
            'P' => 20, // Stock Asset Account
            'Q' => 20, // Sales Account
            'R' => 20, // Purchase Account
            'S' => 15, // Tax Rate
            'T' => 18, // Tax Inclusive
            'U' => 15, // Barcode
            'V' => 18, // Maintain Stock
            'W' => 15, // Is Active
            'X' => 15, // Is Saleable
            'Y' => 18, // Is Purchasable
        ];
    }
}
