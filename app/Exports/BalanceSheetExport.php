<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class BalanceSheetExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $rows = new Collection();

        $rows->push(['Balance Sheet']);
        $rows->push([$this->data['tenant']->name]);
        $rows->push(['As of ' . \Carbon\Carbon::parse($this->data['asOfDate'])->format('F d, Y')]);
        $rows->push(['']);

        $rows->push(['ASSETS', '']);
        $rows->push(['Account Name', 'Amount (₦)']);
        
        foreach ($this->data['assets'] as $item) {
            $rows->push([
                $item['account']->name,
                number_format($item['balance'], 2)
            ]);
        }
        
        $rows->push(['Total Assets', number_format($this->data['totalAssets'], 2)]);
        $rows->push(['']);

        $rows->push(['LIABILITIES', '']);
        $rows->push(['Account Name', 'Amount (₦)']);
        
        foreach ($this->data['liabilities'] as $item) {
            $rows->push([
                $item['account']->name,
                number_format($item['balance'], 2)
            ]);
        }
        
        $rows->push(['Total Liabilities', number_format($this->data['totalLiabilities'], 2)]);
        $rows->push(['']);

        $rows->push(['OWNER\'S EQUITY', '']);
        $rows->push(['Account Name', 'Amount (₦)']);
        
        foreach ($this->data['equity'] as $item) {
            $rows->push([
                $item['account']->name,
                number_format($item['balance'], 2)
            ]);
        }

        if (isset($this->data['retainedEarnings']) && abs($this->data['retainedEarnings']) >= 0.01) {
            $rows->push(['Retained Earnings', number_format($this->data['retainedEarnings'], 2)]);
        }
        
        $rows->push(['Total Equity', number_format($this->data['totalEquity'], 2)]);
        $rows->push(['']);
        $rows->push(['Total Liabilities + Equity', number_format($this->data['totalLiabilities'] + $this->data['totalEquity'], 2)]);

        return $rows;
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['bold' => true, 'size' => 14]],
            3 => ['font' => ['size' => 12]],
        ];
    }

    public function title(): string
    {
        return 'Balance Sheet';
    }
}
