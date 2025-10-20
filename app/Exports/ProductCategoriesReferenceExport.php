<?php

namespace App\Exports;

use App\Models\ProductCategory;
use App\Models\Tenant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductCategoriesReferenceExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $tenant;

    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    public function collection()
    {
        $categories = ProductCategory::where('tenant_id', $this->tenant->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'parent' => $category->parent ? $category->parent->name : 'None',
                'description' => $category->description ?? '-',
                'is_active' => $category->is_active ? 'Yes' : 'No',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Category ID',
            'Category Name',
            'Slug',
            'Parent Category',
            'Description',
            'Is Active',
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
            'A' => 15, // Category ID
            'B' => 30, // Category Name
            'C' => 25, // Slug
            'D' => 25, // Parent Category
            'E' => 40, // Description
            'F' => 12, // Is Active
        ];
    }
}
