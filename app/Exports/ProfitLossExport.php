<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class ProfitLossExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $tenant;
    protected $incomeData;
    protected $expenseData;
    protected $totalIncome;
    protected $totalExpenses;
    protected $netProfit;
    protected $fromDate;
    protected $toDate;

    public function __construct($tenant, $incomeData, $expenseData, $totalIncome, $totalExpenses, $netProfit, $fromDate, $toDate)
    {
        $this->tenant = $tenant;
        $this->incomeData = $incomeData;
        $this->expenseData = $expenseData;
        $this->totalIncome = $totalIncome;
        $this->totalExpenses = $totalExpenses;
        $this->netProfit = $netProfit;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function collection()
    {
        $data = new Collection();

        // Header rows
        $data->push(['Profit & Loss Statement']);
        $data->push([$this->tenant->name ?? 'Company Name']);
        $data->push(['From: ' . \Carbon\Carbon::parse($this->fromDate)->format('F d, Y') . ' To: ' . \Carbon\Carbon::parse($this->toDate)->format('F d, Y')]);
        $data->push(['']); // Empty row

        // Income Section
        $data->push(['INCOME', '']);
        $data->push(['Account Name', 'Amount (₦)']);
        
        foreach ($this->incomeData as $item) {
            $data->push([
                $item['account']->name,
                number_format($item['amount'], 2)
            ]);
        }
        
        $data->push(['Total Income', number_format($this->totalIncome, 2)]);
        $data->push(['']); // Empty row

        // Expenses Section
        $data->push(['EXPENSES', '']);
        $data->push(['Account Name', 'Amount (₦)']);
        
        foreach ($this->expenseData as $item) {
            $data->push([
                $item['account']->name,
                number_format($item['amount'], 2)
            ]);
        }
        
        $data->push(['Total Expenses', number_format($this->totalExpenses, 2)]);
        $data->push(['']); // Empty row

        // Net Profit/Loss
        $data->push([
            'NET ' . ($this->netProfit >= 0 ? 'PROFIT' : 'LOSS'),
            number_format(abs($this->netProfit), 2)
        ]);

        // Summary
        $data->push(['']); // Empty row
        $data->push(['SUMMARY', '']);
        $data->push(['Profit Margin', $this->totalIncome > 0 ? number_format(($this->netProfit / $this->totalIncome) * 100, 2) . '%' : '0.00%']);

        return $data;
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
            5 => ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8F5E9']]],
            6 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Profit & Loss';
    }
}
