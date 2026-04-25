<?php
    // ─── Shared Invoice Data Extraction ──────────────────────────────
    // This partial extracts and prepares all common data used by every invoice template.
    // Include this at the top of each template with a same-scope PHP include so the
    // computed variables remain available in the parent template.

    if (!function_exists('numberToWords')) {
        function numberToWords($number) {
            if ($number == 0) return 'Zero';

            $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
                     'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
                     'Seventeen', 'Eighteen', 'Nineteen'];
            $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

            $number = (int) $number;

            if ($number < 20) return $ones[$number];
            if ($number < 100) return $tens[intval($number / 10)] . ($number % 10 ? ' ' . $ones[$number % 10] : '');
            if ($number < 1000) return $ones[intval($number / 100)] . ' Hundred' . ($number % 100 ? ' and ' . numberToWords($number % 100) : '');
            if ($number < 1000000) return numberToWords(intval($number / 1000)) . ' Thousand' . ($number % 1000 ? ' ' . numberToWords($number % 1000) : '');
            if ($number < 1000000000) return numberToWords(intval($number / 1000000)) . ' Million' . ($number % 1000000 ? ' ' . numberToWords($number % 1000000) : '');

            return 'Amount too large';
        }
    }

    // Extract line items
    $lineItems = collect($invoice->items ?? []);
    if ($lineItems->isEmpty() && !empty($invoice->meta_data)) {
        $metaData = is_array($invoice->meta_data) ? $invoice->meta_data : json_decode($invoice->meta_data, true);
        $lineItems = collect($metaData['inventory_items'] ?? [])->map(function ($item) {
            return is_array($item) ? (object) $item : $item;
        });
    }

    // Also handle $inventoryItems from print view
    if ($lineItems->isEmpty() && isset($inventoryItems) && count($inventoryItems) > 0) {
        $lineItems = collect($inventoryItems)->map(function ($item) {
            return is_array($item) ? (object) $item : $item;
        });
    }

    // Calculate subtotal
    $subtotal = $lineItems->sum(function ($item) {
        if (isset($item->amount)) return (float) $item->amount;
        return (float) ($item->quantity ?? 0) * (float) ($item->rate ?? $item->unit_price ?? 0);
    });

    // Get voucher entries
    $voucherEntries = $invoice->entries;

    // VAT entries
    $vatEntries = $voucherEntries->filter(function($entry) {
        $accountName = strtolower($entry->ledgerAccount->name ?? '');
        $accountCode = $entry->ledgerAccount->code ?? '';
        return str_contains($accountName, 'vat') || in_array($accountCode, ['VAT-OUT-001', 'VAT-IN-001']);
    });

    // Product account IDs
    $productAccountIds = $lineItems->map(function($item) use ($invoice) {
        $productId = is_object($item) ? ($item->product_id ?? null) : ($item['product_id'] ?? null);
        $product = \App\Models\Product::find($productId);
        if (!$product) return null;
        return ($invoice->voucherType && str_contains(strtolower($invoice->voucherType->name ?? ''), 'purchase'))
            ? $product->purchase_account_id : $product->sales_account_id;
    })->filter()->unique()->toArray();

    // Additional charges
    $additionalCharges = $voucherEntries->filter(function($entry) use ($vatEntries, $productAccountIds) {
        if ($vatEntries->contains('id', $entry->id)) return false;
        $account = $entry->ledgerAccount;
        if (!$account) return false;
        if ($account->accountGroup && in_array($account->accountGroup->code, ['AR', 'AP'])) return false;
        if (in_array($account->id, $productAccountIds)) return false;
        $accountName = strtolower($account->name ?? '');
        $accountCode = strtoupper($account->code ?? '');
        if (in_array($accountName, ['cost of goods sold', 'inventory', 'stock']) ||
            in_array($accountCode, ['COGS', 'INV', 'STOCK'])) return false;
        return true;
    });

    // Calculate total
    $totalAmount = $subtotal;
    foreach ($additionalCharges as $charge) {
        $totalAmount += $charge->credit_amount > 0 ? $charge->credit_amount : $charge->debit_amount;
    }
    foreach ($vatEntries as $vatEntry) {
        $totalAmount += $vatEntry->credit_amount > 0 ? $vatEntry->credit_amount : $vatEntry->debit_amount;
    }

    // Customer display name
    $displayName = 'Walk-in Customer';
    if ($customer) {
        if (!empty($customer->company_name)) {
            $displayName = $customer->company_name;
        } else {
            $fullName = trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''));
            $displayName = $fullName !== '' ? $fullName : ($customer->name ?? 'Walk-in Customer');
        }
    }

    // Currency symbol
    $currencySymbol = $tenant->settings['currency_symbol'] ?? '₦';

    // Invoice number
    $invoiceNumber = ($invoice->voucherType->prefix ?? '') . str_pad($invoice->voucher_number, 4, '0', STR_PAD_LEFT);

    // Per-unit totals (grouped by unit symbol, e.g. kg => 30, bags => 7)
    $unitTotalsMap = [];
    foreach ($lineItems as $__uItem) {
        $__type = is_object($__uItem) ? ($__uItem->item_type ?? 'product') : ($__uItem['item_type'] ?? 'product');
        if (strtolower((string) $__type) === 'service') continue;
        $__unit = trim((string) (is_object($__uItem) ? ($__uItem->unit ?? '') : ($__uItem['unit'] ?? '')));
        if ($__unit === '') {
            // Fallback to the product's primary unit symbol when the line item has no stored unit.
            if (is_object($__uItem)) {
                $__unit = trim((string) (optional(optional($__uItem->product ?? null)->primaryUnit)->symbol ?? ''));
            }
        }
        if ($__unit === '') continue;
        $__q = (float) (is_object($__uItem) ? ($__uItem->quantity ?? 0) : ($__uItem['quantity'] ?? 0));
        if ($__q <= 0) continue;
        $unitTotalsMap[$__unit] = ($unitTotalsMap[$__unit] ?? 0) + $__q;
    }
    $unitTotals = collect($unitTotalsMap)->map(function ($qty, $unit) {
        $formatted = (floor($qty) == $qty) ? number_format($qty, 0) : rtrim(rtrim(number_format($qty, 3, '.', ''), '0'), '.');
        return ['unit' => $unit, 'qty' => $qty, 'qty_formatted' => $formatted, 'label' => $formatted . ' ' . $unit];
    })->values();
    $unitTotalsText = $unitTotals->pluck('label')->implode(', ');
?>
