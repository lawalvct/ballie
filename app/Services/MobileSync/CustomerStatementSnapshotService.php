<?php

namespace App\Services\MobileSync;

use App\Models\Customer;
use App\Models\MobileDevice;
use App\Models\MobileDocumentExport;
use App\Models\Tenant;
use App\Models\Voucher;
use App\Models\VoucherEntry;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/**
 * Phase 2: produces a point-in-time customer ledger statement PDF for
 * mobile clients. Re-implements the same statement-builder logic as
 * `Api\Tenant\Crm\CustomerController::buildStatementData` and renders
 * the same `tenant.crm.customers.statement-pdf` blade so the output is
 * byte-identical with what users see on the web.
 *
 * Snapshots are cached in `mobile_document_exports` keyed by
 *   (customer_sync_uuid, period_from, period_to)
 * for `mobile_sync.documents.statement_cache_minutes` (default 1 day).
 */
class CustomerStatementSnapshotService
{
    /**
     * @return array{export: MobileDocumentExport, download_url: string, statement_summary: array}
     */
    public function generateStatement(
        Tenant $tenant,
        Customer $customer,
        string $fromDate,
        string $toDate,
        MobileDevice $device,
    ): array {
        if ($customer->tenant_id !== $tenant->id) {
            throw new \RuntimeException('Customer does not belong to tenant');
        }
        if (!$customer->sync_uuid) {
            throw new \RuntimeException('Customer has no sync_uuid (not yet synced)');
        }
        if (!$customer->ledger_account_id) {
            throw new \RuntimeException('Customer has no ledger account');
        }

        $cacheMinutes = (int) config('mobile_sync.documents.statement_cache_minutes', 60 * 24);

        $existing = MobileDocumentExport::query()
            ->where('tenant_id', $tenant->id)
            ->where('document_type', MobileDocumentExport::TYPE_CUSTOMER_STATEMENT)
            ->where('customer_sync_uuid', $customer->sync_uuid)
            ->where('period_from', $fromDate)
            ->where('period_to', $toDate)
            ->where('status', MobileDocumentExport::STATUS_READY)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->latest('id')
            ->first();

        if ($existing) {
            return [
                'export' => $existing,
                'download_url' => $this->signedUrl($existing),
                'statement_summary' => $existing->meta['summary'] ?? [],
            ];
        }

        $statementData = $this->buildStatementData($tenant, $customer, $fromDate, $toDate);

        $pdf = Pdf::loadView('tenant.crm.customers.statement-pdf', [
            'tenant'          => $tenant,
            'customer'        => $statementData['customer'],
            'ledgerAccount'   => $statementData['ledger_account'],
            'period'          => $statementData['period'],
            'openingBalance'  => $statementData['opening_balance'],
            'totalDebits'     => $statementData['total_debits'],
            'totalCredits'    => $statementData['total_credits'],
            'closingBalance'  => $statementData['closing_balance'],
            'transactions'    => $statementData['transactions'],
        ]);
        $bytes = $pdf->output();

        $exportUuid = (string) Str::uuid();
        $disk = config('mobile_sync.documents.disk', 'local');
        $dir = trim(config('mobile_sync.documents.directory', 'mobile-exports'), '/') . '/statements';
        $storagePath = $dir . '/' . $exportUuid . '.pdf';

        Storage::disk($disk)->put($storagePath, $bytes);

        $filename = 'customer-statement-' . $customer->id . '-' . $fromDate . '-to-' . $toDate . '.pdf';

        $summary = [
            'opening_balance' => (float) $statementData['opening_balance'],
            'total_debits'    => (float) $statementData['total_debits'],
            'total_credits'   => (float) $statementData['total_credits'],
            'closing_balance' => (float) $statementData['closing_balance'],
            'transaction_count' => count($statementData['transactions']),
        ];

        $export = MobileDocumentExport::create([
            'export_uuid'        => $exportUuid,
            'tenant_id'          => $tenant->id,
            'user_id'            => $device->user_id,
            'mobile_device_id'   => $device->id,
            'document_type'      => MobileDocumentExport::TYPE_CUSTOMER_STATEMENT,
            'source_table'       => 'customers',
            'source_server_id'   => $customer->id,
            'customer_sync_uuid' => $customer->sync_uuid,
            'disk'               => $disk,
            'storage_path'       => $storagePath,
            'file_name'          => $filename,
            'mime_type'          => 'application/pdf',
            'file_size'          => strlen($bytes),
            'status'             => MobileDocumentExport::STATUS_READY,
            'period_from'        => $fromDate,
            'period_to'          => $toDate,
            'generated_at'       => now(),
            'expires_at'         => Carbon::now()->addMinutes($cacheMinutes),
            'meta'               => ['summary' => $summary],
        ]);

        return [
            'export' => $export,
            'download_url' => $this->signedUrl($export),
            'statement_summary' => $summary,
        ];
    }

    public function signedUrl(MobileDocumentExport $export): string
    {
        $ttl = (int) config('mobile_sync.documents.signed_url_ttl_minutes', 30);

        return URL::temporarySignedRoute(
            'api.v1.tenant.sync.documents.download',
            Carbon::now()->addMinutes($ttl),
            [
                'tenant'      => $export->tenant->slug ?? $export->tenant_id,
                'export_uuid' => $export->export_uuid,
            ],
        );
    }

    /**
     * Mirrors `Api\Tenant\Crm\CustomerController::buildStatementData`
     * exactly, so the PDF blade receives the same data shape it does on
     * web. Inlined to avoid coupling a service to an HTTP controller.
     *
     * @return array{customer: Customer, ledger_account: mixed, period: array, opening_balance: float, total_debits: float, total_credits: float, closing_balance: float, transactions: array}
     */
    private function buildStatementData(Tenant $tenant, Customer $customer, string $startDate, string $endDate): array
    {
        $ledgerAccount = $customer->ledgerAccount;

        $opening = VoucherEntry::where('ledger_account_id', $ledgerAccount->id)
            ->whereHas('voucher', function ($q) use ($tenant, $startDate) {
                $q->where('tenant_id', $tenant->id)
                    ->where('status', Voucher::STATUS_POSTED)
                    ->where('voucher_date', '<', $startDate);
            })
            ->selectRaw('SUM(debit_amount) as total_debits, SUM(credit_amount) as total_credits')
            ->first();

        $openingBalanceAmount = (float) (($opening->total_debits ?? 0) - ($opening->total_credits ?? 0));

        $transactions = VoucherEntry::with(['voucher.voucherType'])
            ->where('ledger_account_id', $ledgerAccount->id)
            ->whereHas('voucher', function ($q) use ($tenant, $startDate, $endDate, $ledgerAccount) {
                $q->where('tenant_id', $tenant->id)
                    ->where('status', Voucher::STATUS_POSTED)
                    ->where('id', '!=', $ledgerAccount->opening_balance_voucher_id)
                    ->whereBetween('voucher_date', [$startDate, $endDate]);
            })
            ->orderBy('id')
            ->get();

        $running = $openingBalanceAmount;
        $rows = [];
        foreach ($transactions as $tx) {
            $running += ($tx->debit_amount - $tx->credit_amount);
            $rows[] = [
                'date' => optional($tx->voucher->voucher_date)->format('Y-m-d'),
                'particulars' => $tx->particulars ?? ($tx->voucher->voucherType->name ?? null),
                'voucher_type' => $tx->voucher->voucherType->name ?? null,
                'voucher_number' => ($tx->voucher->voucherType->prefix ?? '') . $tx->voucher->voucher_number,
                'debit' => (float) $tx->debit_amount,
                'credit' => (float) $tx->credit_amount,
                'running_balance' => (float) $running,
            ];
        }

        return [
            'customer' => $customer,
            'ledger_account' => $ledgerAccount,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'opening_balance' => $openingBalanceAmount,
            'total_debits' => (float) collect($rows)->sum('debit'),
            'total_credits' => (float) collect($rows)->sum('credit'),
            'closing_balance' => (float) $running,
            'transactions' => $rows,
        ];
    }
}
