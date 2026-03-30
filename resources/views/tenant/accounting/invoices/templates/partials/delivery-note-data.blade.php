<?php
    // ─── Shared Delivery Note Data Extraction ────────────────────────
    // Extracts line items (physical products only) without amounts.
    // Include via: @php include resource_path('views/tenant/accounting/invoices/templates/partials/delivery-note-data.blade.php'); @endphp

    // Extract line items
    $allItems = collect($invoice->items ?? []);
    if ($allItems->isEmpty() && !empty($invoice->meta_data)) {
        $metaData = is_array($invoice->meta_data) ? $invoice->meta_data : json_decode($invoice->meta_data, true);
        $allItems = collect($metaData['inventory_items'] ?? [])->map(function ($item) {
            return is_array($item) ? (object) $item : $item;
        });
    }

    // Also handle $inventoryItems from print view
    if ($allItems->isEmpty() && isset($inventoryItems) && count($inventoryItems) > 0) {
        $allItems = collect($inventoryItems)->map(function ($item) {
            return is_array($item) ? (object) $item : $item;
        });
    }

    // Filter to physical/product items only (exclude services)
    $deliveryItems = $allItems->filter(function ($item) {
        $type = $item->item_type ?? null;
        // Include if item_type is 'product', null, or empty (default to physical)
        // Exclude only explicitly marked 'service' items
        return strtolower((string) $type) !== 'service';
    })->values();

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

    // Invoice number
    $invoiceNumber = ($invoice->voucherType->prefix ?? '') . str_pad($invoice->voucher_number, 4, '0', STR_PAD_LEFT);

    // Total quantity for summary
    $totalQuantity = $deliveryItems->sum(function ($item) {
        return (float) ($item->quantity ?? 0);
    });
?>
