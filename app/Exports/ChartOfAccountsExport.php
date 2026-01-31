<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ChartOfAccountsExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    protected array $headings = [];
    protected array $rows = [];
    protected int $totalsRowIndex = 0;

    public function __construct(Collection $accounts, Collection $accountGroups)
    {
        $groupedAccounts = $accounts->groupBy('account_group_id');

        $activeGroups = $accountGroups->filter(function ($group) use ($groupedAccounts) {
            return $groupedAccounts->has($group->id);
        })->values();

        $this->headings = array_merge(['S/N'], $activeGroups->pluck('name')->all());

        $maxAccounts = $groupedAccounts->map->count()->max() ?? 0;

        for ($i = 0; $i < $maxAccounts; $i++) {
            $row = [$i + 1];

            foreach ($activeGroups as $group) {
                $groupAccounts = $groupedAccounts->get($group->id, collect())->values();
                $account = $groupAccounts->get($i);

                if ($account) {
                    $balance = $account->current_balance ?? 0;
                    $row[] = sprintf('%s - %s (%s)',
                        $account->code,
                        $account->name,
                        number_format($balance, 2)
                    );
                } else {
                    $row[] = '';
                }
            }

            $this->rows[] = $row;
        }

        $totalsRow = ['Total'];
        foreach ($activeGroups as $group) {
            $groupTotal = $groupedAccounts->get($group->id, collect())->sum('current_balance');
            $totalsRow[] = number_format($groupTotal, 2);
        }

        $this->rows[] = $totalsRow;

        $this->totalsRowIndex = count($this->rows) + 1; // +1 for headings row
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
            $this->totalsRowIndex => ['font' => ['bold' => true]],
        ];
    }
}