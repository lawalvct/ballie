<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VouchersExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $vouchers;

    public function __construct($vouchers)
    {
        $this->vouchers = $vouchers;
    }

    public function collection()
    {
        return $this->vouchers;
    }

    public function headings(): array
    {
        return [
            'Voucher Number',
            'Type',
            'Date',
            'Reference',
            'Narration',
            'First Particular',
            'Ledger Account',
            'Amount',
            'Status',
            'Created By',
        ];
    }

    public function map($voucher): array
    {
        $firstEntry = $voucher->entries->first();
        $firstParticular = $firstEntry?->particulars ?: ($firstEntry?->ledgerAccount?->name ?? '');

        return [
            $voucher->voucher_number,
            $voucher->voucherType?->name ?? 'N/A',
            optional($voucher->voucher_date)->format('Y-m-d'),
            $voucher->reference_number ?? '',
            $voucher->narration ?? '',
            $firstParticular,
            $firstEntry?->ledgerAccount?->name ?? '',
            number_format($voucher->total_amount, 2),
            ucfirst($voucher->status),
            $voucher->createdBy?->name ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
