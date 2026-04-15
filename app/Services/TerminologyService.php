<?php

namespace App\Services;

use App\Models\Tenant;

/**
 * Maps UI labels to business-category-specific terminology.
 *
 * A law firm says "clients" and "revenue", not "customers" and "sales".
 * This service provides the right term for the current tenant's business category.
 *
 * Usage in Blade: {{ $term->label('crm') }}   → "Clients" (for service)
 * Usage in PHP:   app(TerminologyService::class)->label('sales')
 */
class TerminologyService
{
    protected string $category;

    public function __construct(?Tenant $tenant = null)
    {
        $this->category = $tenant?->getBusinessCategory() ?? 'hybrid';
    }

    /**
     * Get the display label for a term based on the current business category.
     */
    public function label(string $key): string
    {
        return static::LABELS[$this->category][$key]
            ?? static::LABELS['trading'][$key]
            ?? $key;
    }

    /**
     * Get the current business category.
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * Get all labels for the current category.
     */
    public function all(): array
    {
        return static::LABELS[$this->category] ?? static::LABELS['trading'];
    }

    /**
     * Check if a term should be hidden for this category.
     */
    public function isHidden(string $key): bool
    {
        $label = static::LABELS[$this->category][$key] ?? null;

        return $label === null || $label === '_(hidden)_';
    }

    /**
     * Static helper for Blade directive @term('key').
     * Resolves the label for the current tenant context.
     */
    public static function resolve(string $key, ?Tenant $tenant = null): string
    {
        $category = $tenant?->getBusinessCategory() ?? 'hybrid';

        return static::LABELS[$category][$key]
            ?? static::LABELS['trading'][$key]
            ?? $key;
    }

    /**
     * Label mapping per business category.
     *
     * Each category defines how UI terms should be displayed.
     * Falls back to 'trading' if a key isn't defined for a category.
     */
    public const LABELS = [
        'trading' => [
            'crm'             => 'Customers',
            'statutory'       => 'Tax',
            'sales'           => 'Sales',
            'customers'       => 'Customers',
            'customer'        => 'Customer',
            'products'        => 'Products',
            'product'         => 'Product',
            'quotation'       => 'Quotation',
            'quotations'      => 'Quotations',
            'sales_invoice'   => 'Sales Invoice',
            'sales_invoices'  => 'Sales Invoices',
            'purchase_invoice'=> 'Purchase Invoice',
            'cogs'            => 'Cost of Goods Sold',
            'purchase'        => 'Purchase',
            'purchases'       => 'Purchases',
            'purchase_order'  => 'Purchase Order',
            'revenue'         => 'Sales Revenue',
            'line_item'       => 'Line Item',
            'pnl_title'       => 'Profit or Loss Statement',
            'sales_reports'   => 'Sales Reports',
            'purchase_reports'=> 'Purchase Reports',
            'top_products'    => 'Top Selling Products',
            'todays_sales'    => "Today's Sales",
            'low_stock'       => 'Low Stock Alert',
            'stock'           => 'Stock',
            'units_sold'      => 'Units Sold',
            'sales_order'     => 'Sales Order',
            'sales_return_invoice' => 'Sales Return',
            'purchase_return_invoice' => 'Purchase Return',
            'balance_sheet'   => 'Statement of Financial Position',
            'trial_balance'   => 'Trial Balance',
            'cash_flow'       => 'Statement of Cash Flows',
            'equity_report'   => 'Statement of Changes in Equity',
        ],
        'manufacturing' => [
            'crm'             => 'Customers',
            'statutory'       => 'Tax',
            'sales'           => 'Sales',
            'customers'       => 'Customers',
            'customer'        => 'Customer',
            'products'        => 'Products',
            'product'         => 'Product',
            'quotation'       => 'Quotation',
            'quotations'      => 'Quotations',
            'sales_invoice'   => 'Sales Invoice',
            'sales_invoices'  => 'Sales Invoices',
            'purchase_invoice'=> 'Purchase Invoice',
            'cogs'            => 'Cost of Production',
            'purchase'        => 'Raw Material Purchase',
            'purchases'       => 'Raw Material Purchases',
            'purchase_order'  => 'Purchase Order',
            'revenue'         => 'Sales Revenue',
            'line_item'       => 'Line Item',
            'pnl_title'       => 'Profit & Loss Statement',
            'sales_reports'   => 'Sales Reports',
            'purchase_reports'=> 'Purchase Reports',
            'top_products'    => 'Top Products',
            'todays_sales'    => "Today's Production",
            'low_stock'       => 'Low Stock Alert',
            'stock'           => 'Raw Materials / Stock',
            'units_sold'      => 'Units Produced & Sold',
            'sales_order'     => 'Sales Order',
            'sales_return_invoice' => 'Sales Return',
            'purchase_return_invoice' => 'Purchase Return',
            'balance_sheet'   => 'Statement of Financial Position',
            'trial_balance'   => 'Trial Balance',
            'cash_flow'       => 'Statement of Cash Flows',
            'equity_report'   => 'Statement of Changes in Equity',
        ],
        'service' => [
            'crm'             => 'Clients',
            'statutory'       => 'Tax',
            'sales'           => 'Revenue',
            'customers'       => 'Clients',
            'customer'        => 'Client',
            'products'        => 'Services',
            'product'         => 'Service',
            'quotation'       => 'Proposal',
            'quotations'      => 'Proposals',
            'sales_invoice'   => 'Invoice',
            'sales_invoices'  => 'Invoices',
            'purchase_invoice'=> 'Expense / Bill',
            'cogs'            => 'Direct Costs',
            'purchase'        => 'Expense',
            'purchases'       => 'Expenses',
            'purchase_order'  => 'Purchase Order',
            'revenue'         => 'Service Revenue',
            'line_item'       => 'Line Item',
            'pnl_title'       => 'Income Statement',
            'sales_reports'   => 'Revenue Reports',
            'purchase_reports'=> 'Expense Reports',
            'top_products'    => 'Top Services',
            'todays_sales'    => "Today's Revenue",
            'low_stock'       => null, // hidden for service
            'stock'           => null, // hidden for service
            'units_sold'      => 'Hours / Sessions',
            'sales_order'     => 'Engagement / Contract',
            'sales_return_invoice' => 'Service Return',
            'purchase_return_invoice' => 'Expense Return',
            'balance_sheet'   => 'Statement of Financial Position',
            'trial_balance'   => 'Trial Balance',
            'cash_flow'       => 'Statement of Cash Flows',
            'equity_report'   => 'Statement of Changes in Equity',
        ],
        'hybrid' => [
            'crm'             => 'CRM',
            'statutory'       => 'Tax',
            'sales'           => 'Revenue',
            'customers'       => 'Clients',
            'customer'        => 'Client',
            'products'        => 'Inventory',
            'product'         => 'Item',
            'quotation'       => 'Proposal',
            'quotations'      => 'Proposals',
            'sales_invoice'   => 'Invoice',
            'sales_invoices'  => 'Invoices',
            'purchase_invoice'=> 'Purchase / Bill',
            'cogs'            => 'Cost of Sales',
            'purchase'        => 'Purchase',
            'purchases'       => 'Purchases',
            'purchase_order'  => 'Purchase Order',
            'revenue'         => 'Revenue',
            'line_item'       => 'Line Item',
            'pnl_title'       => 'Profit or Loss Statement',
            'sales_reports'   => 'Revenue Reports',
            'purchase_reports'=> 'Expense Reports',
            'top_products'    => 'Top Items',
            'todays_sales'    => "Today's Revenue",
            'low_stock'       => 'Low Stock Alert',
            'stock'           => 'Stock',
            'units_sold'      => 'Units / Hours',
            'sales_order'     => 'Sales Order',
            'sales_return_invoice' => 'Sales Return',
            'purchase_return_invoice' => 'Purchase Return',
            'balance_sheet'   => 'Statement of Financial Position',
            'trial_balance'   => 'Trial Balance',
            'cash_flow'       => 'Statement of Cash Flows',
            'equity_report'   => 'Statement of Changes in Equity',
        ],
    ];
}
