<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoicesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $invoices;

    public function __construct($invoices)
    {
        $this->invoices = $invoices;
    }

    public function collection()
    {
        return $this->invoices;
    }

    public function headings(): array
    {
        return [
            'Invoice #',
            'Type',
            'Date',
            'Customer/Vendor',
            'Reference',
            'Description',
            'Amount',
            'Status',
            'Created By',
        ];
    }

    public function map($invoice): array
    {
        $displayNumber = ($invoice->voucherType?->prefix ?? $invoice->voucherType?->abbreviation ?? '') . $invoice->voucher_number;
        $type = ($invoice->voucherType?->inventory_effect ?? '') === 'increase' ? 'Purchase' : 'Sales';
        $partyEntry = $invoice->entries->where('debit_amount', '>', 0)->first();
        $partyName = $partyEntry?->ledgerAccount?->name ?? 'Cash Sale';

        return [
            $displayNumber,
            $type,
            optional($invoice->voucher_date)->format('Y-m-d'),
            $partyName,
            $invoice->reference_number ?? '',
            $invoice->narration ?? '',
            number_format($invoice->total_amount, 2),
            ucfirst($invoice->status),
            $invoice->createdBy?->name ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
