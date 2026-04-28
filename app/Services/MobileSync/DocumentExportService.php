<?php

namespace App\Services\MobileSync;

use App\Models\Bank;
use App\Models\Customer;
use App\Models\MobileDevice;
use App\Models\MobileDocumentExport;
use App\Models\Tenant;
use App\Models\Voucher;
use App\Services\ModuleRegistry;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/**
 * Phase 2: generates and caches the official invoice PDF for a synced
 * voucher so mobile clients can open the same file the web app produces.
 *
 * Reuses the exact view selection and data shape from the existing
 * web `Tenant\Accounting\InvoiceController::pdf` method:
 *   - Tenant settings.invoice_template -> ballie | tally | zoho | sage | quickbooks
 *   - Bank from settings.invoice_bank_account_id
 *   - Custom terms, payment links (if online_payments module enabled)
 *
 * Cache key = voucher.sync_uuid + voucher.server_version. Re-uses an
 * existing `mobile_document_exports` row if not yet expired.
 */
class DocumentExportService
{
    /**
     * Generate (or fetch from cache) the official invoice PDF and return
     * the export row plus a temporary signed download URL.
     *
     * @return array{export: MobileDocumentExport, download_url: string}
     */
    public function generateInvoicePdf(Tenant $tenant, Voucher $invoice, MobileDevice $device): array
    {
        if ($invoice->tenant_id !== $tenant->id) {
            throw new \RuntimeException('Voucher does not belong to tenant');
        }
        if (!$invoice->sync_uuid) {
            throw new \RuntimeException('Voucher has no sync_uuid (not yet synced)');
        }

        $cacheMinutes = (int) config('mobile_sync.documents.invoice_pdf_cache_minutes', 60 * 24 * 7);
        $existing = MobileDocumentExport::query()
            ->where('tenant_id', $tenant->id)
            ->where('document_type', MobileDocumentExport::TYPE_INVOICE_PDF)
            ->where('source_table', 'vouchers')
            ->where('source_sync_uuid', $invoice->sync_uuid)
            ->where('status', MobileDocumentExport::STATUS_READY)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->latest('id')
            ->first();

        // Invalidate cache if voucher changed since export generated
        $cachedVersion = (int) ($existing?->meta['source_server_version'] ?? 0);
        if ($existing && $cachedVersion === (int) ($invoice->server_version ?? 0)) {
            return [
                'export' => $existing,
                'download_url' => $this->signedUrl($existing),
            ];
        }

        $invoice->load(['voucherType', 'entries.ledgerAccount', 'createdBy', 'postedBy', 'items']);

        // ── Resolve customer/vendor for blade ────────────────────────────
        $isSales = ($invoice->voucherType->inventory_effect ?? null) === 'decrease';
        $partyEntry = $isSales
            ? $invoice->entries->where('debit_amount', '>', 0)->first()
            : $invoice->entries->where('credit_amount', '>', 0)->first();

        $customer = null;
        $partyNameForFile = 'party';
        if ($partyEntry && $partyEntry->ledgerAccount) {
            $ledger = $partyEntry->ledgerAccount;
            $custModel = \App\Models\Customer::where('ledger_account_id', $ledger->id)->first()
                ?? \App\Models\Vendor::where('ledger_account_id', $ledger->id)->first();
            if ($custModel) {
                $customer = $custModel;
                $partyNameForFile = $custModel->company_name
                    ?? trim(($custModel->first_name ?? '') . ' ' . ($custModel->last_name ?? ''))
                    ?: ($custModel->name ?? $ledger->name ?? 'party');
            } else {
                $customer = $ledger;
                $partyNameForFile = $ledger->name ?? 'party';
            }
        }

        // ── View selection (same logic as web pdf()) ─────────────────────
        $template = $tenant->settings['invoice_template'] ?? 'ballie';
        $allowed = ['ballie', 'tally', 'zoho', 'sage', 'quickbooks'];
        if (!in_array($template, $allowed, true)) {
            $template = 'ballie';
        }
        $view = $template === 'ballie'
            ? 'tenant.accounting.invoices.pdf'
            : 'tenant.accounting.invoices.templates.' . $template;

        // ── Optional bank, terms, payment links ──────────────────────────
        $invoiceBank = null;
        if (!empty($tenant->settings['invoice_bank_account_id'])) {
            $invoiceBank = Bank::where('id', $tenant->settings['invoice_bank_account_id'])
                ->where('tenant_id', $tenant->id)
                ->first();
        }
        $invoiceTerms = $tenant->settings['invoice_terms'] ?? null;

        $paymentLinks = [];
        $onlinePaymentsEnabled = false;
        if (class_exists(ModuleRegistry::class)) {
            try {
                $onlinePaymentsEnabled = ModuleRegistry::isModuleEnabled($tenant, 'online_payments');
            } catch (\Throwable $e) {
                $onlinePaymentsEnabled = false;
            }
        }
        if ($onlinePaymentsEnabled) {
            $meta = is_array($invoice->meta_data)
                ? $invoice->meta_data
                : (is_string($invoice->meta_data) ? json_decode($invoice->meta_data, true) : []);
            $paymentLinks = $meta['payment_links'] ?? [];
        }

        // ── Render PDF ───────────────────────────────────────────────────
        $pdf = Pdf::loadView($view, compact(
            'tenant', 'invoice', 'customer', 'invoiceBank', 'invoiceTerms',
            'paymentLinks', 'onlinePaymentsEnabled'
        ));
        $bytes = $pdf->output();

        // ── Persist file + export row ────────────────────────────────────
        $exportUuid = (string) Str::uuid();
        $disk = config('mobile_sync.documents.disk', 'local');
        $dir = trim(config('mobile_sync.documents.directory', 'mobile-exports'), '/') . '/invoices';
        $filename = $exportUuid . '.pdf';
        $storagePath = $dir . '/' . $filename;

        Storage::disk($disk)->put($storagePath, $bytes);

        $companySegment = Str::slug($tenant->name ?: ($tenant->slug ?? 'company')) ?: 'company';
        $partySegment = Str::slug($partyNameForFile) ?: 'party';
        $docType = $isSales ? 'sales-invoice' : 'purchase-invoice';
        $downloadFilename = $companySegment . '_' . $partySegment . '_' . $docType . '_' . $invoice->voucher_number . '.pdf';

        $export = MobileDocumentExport::create([
            'export_uuid'         => $exportUuid,
            'tenant_id'           => $tenant->id,
            'user_id'             => $device->user_id,
            'mobile_device_id'    => $device->id,
            'document_type'       => MobileDocumentExport::TYPE_INVOICE_PDF,
            'source_table'        => 'vouchers',
            'source_server_id'    => $invoice->id,
            'source_sync_uuid'    => $invoice->sync_uuid,
            'disk'                => $disk,
            'storage_path'        => $storagePath,
            'file_name'           => $downloadFilename,
            'mime_type'           => 'application/pdf',
            'file_size'           => strlen($bytes),
            'status'              => MobileDocumentExport::STATUS_READY,
            'generated_at'        => now(),
            'expires_at'          => Carbon::now()->addMinutes($cacheMinutes),
            'meta'                => [
                'source_server_version' => (int) ($invoice->server_version ?? 0),
                'voucher_number' => $invoice->voucher_number,
                'voucher_type_code' => $invoice->voucherType->code ?? null,
                'template' => $template,
            ],
        ]);

        return [
            'export' => $export,
            'download_url' => $this->signedUrl($export),
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
}
