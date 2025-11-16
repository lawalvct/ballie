<?php

namespace App\Imports;

use App\Models\LedgerAccount;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;

class PaymentEntriesImport implements ToCollection, WithHeadingRow
{
    protected $tenantId;
    protected $entries = [];
    protected $errors = [];
    protected $ledgerAccounts;

    public function __construct($tenantId)
    {
        $this->tenantId = $tenantId;
        // Pre-load all tenant ledger accounts for faster matching
        $this->ledgerAccounts = LedgerAccount::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->get()
            ->keyBy(function ($account) {
                return strtolower(trim($account->name));
            });
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 because of header row and 0-based index

            try {
                // Validate required fields
                if (empty($row['date']) || empty($row['ledger']) || empty($row['amount'])) {
                    $this->errors[] = [
                        'row' => $rowNumber,
                        'message' => 'Missing required fields (date, ledger, or amount)'
                    ];
                    continue;
                }

                // Parse and validate date
                $date = $this->parseDate($row['date']);
                if (!$date) {
                    $this->errors[] = [
                        'row' => $rowNumber,
                        'message' => 'Invalid date format. Use DD-MM-YY or DD/MM/YYYY'
                    ];
                    continue;
                }

                // Validate amount
                $amount = floatval($row['amount']);
                if ($amount <= 0) {
                    $this->errors[] = [
                        'row' => $rowNumber,
                        'message' => 'Amount must be greater than zero'
                    ];
                    continue;
                }

                // Match ledger account
                $ledgerName = strtolower(trim($row['ledger']));
                $ledgerAccount = $this->ledgerAccounts->get($ledgerName);

                if (!$ledgerAccount) {
                    // Try fuzzy matching
                    $ledgerAccount = $this->fuzzyMatchLedger($ledgerName);
                }

                if (!$ledgerAccount) {
                    $this->errors[] = [
                        'row' => $rowNumber,
                        'message' => "Ledger account '{$row['ledger']}' not found"
                    ];
                    continue;
                }

                // Add to entries
                $this->entries[] = [
                    'row' => $rowNumber,
                    'date' => $date,
                    'ledger_account_id' => $ledgerAccount->id,
                    'ledger_name' => $ledgerAccount->name,
                    'particulars' => $row['description'] ?? '',
                    'debit_amount' => $amount,
                    'credit_amount' => 0,
                ];

            } catch (\Exception $e) {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'message' => 'Error processing row: ' . $e->getMessage()
                ];
            }
        }
    }

    protected function parseDate($dateValue)
    {
        try {
            // Try multiple date formats
            $formats = ['d-m-y', 'd/m/Y', 'd-m-Y', 'Y-m-d', 'd/m/y'];

            foreach ($formats as $format) {
                $date = Carbon::createFromFormat($format, $dateValue);
                if ($date) {
                    return $date->format('Y-m-d');
                }
            }

            // Try parsing as Excel date (numeric)
            if (is_numeric($dateValue)) {
                $date = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateValue));
                return $date->format('Y-m-d');
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function fuzzyMatchLedger($searchName)
    {
        $bestMatch = null;
        $bestScore = 0;

        foreach ($this->ledgerAccounts as $account) {
            $accountName = strtolower(trim($account->name));

            // Calculate similarity
            similar_text($searchName, $accountName, $percent);

            // Check for partial match
            $containsMatch = str_contains($accountName, $searchName) || str_contains($searchName, $accountName);

            if ($percent > 85 || ($containsMatch && $percent > 70)) {
                if ($percent > $bestScore) {
                    $bestScore = $percent;
                    $bestMatch = $account;
                }
            }
        }

        return $bestMatch;
    }

    public function getEntries()
    {
        return $this->entries;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function hasErrors()
    {
        return count($this->errors) > 0;
    }

    public function getTotalAmount()
    {
        return array_sum(array_column($this->entries, 'debit_amount'));
    }
}
