# Sales Reports Documentation

## Overview

Comprehensive sales reporting module for the Ballie ERP system, providing detailed insights into sales performance, customer behavior, product profitability, and time-based trends.

## Reports Implemented

### 1. Sales Summary Report

**Route:** `/reports/sales-summary`
**Controller:** `SalesReportsController@salesSummary`
**View:** `tenant.reports.sales.summary`

#### Features:

-   **Key Metrics Dashboard:**

    -   Total sales revenue for the period
    -   Number of invoices generated
    -   Average sale value per transaction
    -   Collection rate (payment status)

-   **Top Performers:**

    -   Top 10 selling products by revenue
    -   Top 10 customers by total purchases

-   **Sales Trend Analysis:**

    -   Daily, weekly, or monthly breakdowns
    -   Sales progression over time
    -   Invoice count per period

-   **Period Comparison:**
    -   Compare with previous period
    -   Growth rate calculation
    -   Performance indicators

#### Filters:

-   Date range (from/to)
-   Grouping (day, week, month)

#### Use Cases:

-   Monthly sales performance review
-   Identifying top revenue sources
-   Tracking sales velocity
-   Strategic planning sessions

---

### 2. Customer Sales Report

**Route:** `/reports/customer-sales`
**Controller:** `SalesReportsController@customerSales`
**View:** `tenant.reports.sales.customer-sales`

#### Features:

-   **Customer Performance Metrics:**

    -   Total sales per customer
    -   Invoice count
    -   Average sale value
    -   First and last sale dates
    -   Contact information

-   **Summary Statistics:**

    -   Total active customers
    -   Total revenue
    -   Average revenue per customer

-   **Sortable Columns:**
    -   Sort by total sales
    -   Sort by invoice count
    -   Sort by average sale value

#### Filters:

-   Date range
-   Specific customer selection
-   Sort order (ascending/descending)

#### Use Cases:

-   Identifying high-value customers
-   Customer retention analysis
-   Account management prioritization
-   Sales territory evaluation

---

### 3. Product Sales Report

**Route:** `/reports/product-sales`
**Controller:** `SalesReportsController@productSales`
**View:** `tenant.reports.sales.product-sales`

#### Features:

-   **Product Performance Analysis:**

    -   Quantity sold
    -   Total revenue
    -   Average selling price
    -   Total cost
    -   Gross profit
    -   Profit margin percentage
    -   Number of invoices

-   **Profitability Indicators:**

    -   Color-coded profit margins:
        -   Green (≥20%): Excellent
        -   Yellow (10-19%): Good
        -   Red (<10%): Needs attention

-   **Category Analysis:**
    -   Filter by product category
    -   Category performance comparison

#### Filters:

-   Date range
-   Product selection
-   Category selection
-   Sort by (revenue, quantity, profit)

#### Use Cases:

-   Product portfolio optimization
-   Pricing strategy review
-   Inventory planning
-   Profitability analysis
-   Stock replenishment decisions

---

### 4. Sales by Period Report

**Route:** `/reports/sales-by-period`
**Controller:** `SalesReportsController@salesByPeriod`
**View:** `tenant.reports.sales.by-period`

#### Features:

-   **Time-Based Analysis:**

    -   Daily breakdown
    -   Weekly aggregation
    -   Monthly summaries
    -   Quarterly reports
    -   Yearly comparisons

-   **Comparative Analytics:**

    -   Compare with previous period
    -   Year-over-year comparison
    -   Growth rate calculation

-   **Performance Insights:**
    -   Best performing period
    -   Worst performing period
    -   Average per period
    -   Trend indicators

#### Filters:

-   Date range
-   Period type (daily, weekly, monthly, quarterly, yearly)
-   Comparison option (previous period, previous year, none)

#### Use Cases:

-   Seasonal trend analysis
-   Budget vs actual comparison
-   Forecasting and projections
-   Business cycle identification
-   Performance benchmarking

---

## Technical Implementation

### Database Queries

All reports use efficient SQL queries with proper indexing:

-   Sales data filtered by `voucher_type.inventory_effect = 'decrease'`
-   Only `posted` vouchers are included
-   Date filtering on `voucher_date`
-   Proper joins with ledger accounts and products

### Performance Optimization

-   **Caching:** Report data can be cached for frequently accessed reports
-   **Pagination:** Large datasets paginated (20 items per page)
-   **Eager Loading:** Relationships preloaded to avoid N+1 queries
-   **Aggregations:** Database-level aggregations for better performance

### Data Sources

1. **Vouchers Table:** Invoice header information
2. **Invoice Items Table:** Line-level product sales data
3. **Voucher Entries Table:** Accounting entries for customer identification
4. **Ledger Accounts Table:** Customer/vendor information
5. **Products Table:** Product details, costs, and pricing
6. **Product Categories Table:** Category classification

---

## Report Filters

### Common Filters (All Reports)

```php
- from_date: Start date (default: start of current month)
- to_date: End date (default: today)
```

### Sales Summary Filters

```php
- group_by: 'day' | 'week' | 'month' (default: 'day')
```

### Customer Sales Filters

```php
- customer_id: Specific customer (optional)
- sort_by: 'total_sales' | 'invoice_count' | 'avg_sale'
- sort_order: 'asc' | 'desc'
```

### Product Sales Filters

```php
- product_id: Specific product (optional)
- category_id: Product category (optional)
- sort_by: 'total_revenue' | 'quantity_sold' | 'gross_profit'
- sort_order: 'asc' | 'desc'
```

### Sales by Period Filters

```php
- period_type: 'daily' | 'weekly' | 'monthly' | 'quarterly' | 'yearly'
- compare_with: null | 'previous_period' | 'previous_year'
```

---

## Key Metrics Explained

### Total Sales

Sum of all posted sales invoices within the date range, excluding drafts and cancelled invoices.

### Average Sale Value

Total sales divided by number of invoices. Indicates transaction size.

### Collection Rate

Percentage of sales that have been paid. Calculated from receipt vouchers linked to invoices.

### Gross Profit

Revenue minus cost of goods sold (COGS). Uses purchase rates from invoice items.

### Profit Margin

Gross profit as a percentage of revenue: `(Gross Profit / Revenue) × 100`

### Growth Rate

Percentage change from comparison period: `((Current - Previous) / Previous) × 100`

---

## Business Intelligence

### Sales Summary - Insights

-   **Rising average sale value:** Indicates successful upselling or premium products
-   **Declining invoice count:** May signal customer attrition or market issues
-   **Low collection rate:** Cash flow concerns, credit policy review needed

### Customer Sales - Insights

-   **Top 20% of customers:** Often generate 80% of revenue (Pareto principle)
-   **First/last sale gap:** Identifies inactive customers for re-engagement
-   **Low average sale:** Opportunity for account growth

### Product Sales - Insights

-   **High volume, low margin:** Commodity products, consider price optimization
-   **Low volume, high margin:** Specialty products, marketing opportunity
-   **Negative margins:** Pricing errors or outdated cost data

### Sales by Period - Insights

-   **Seasonal patterns:** Stock and staff accordingly
-   **Growth trends:** Validate business strategy
-   **Anomalies:** Investigate unusual spikes or drops

---

## Export Capabilities (Future Enhancement)

### Planned Features

1. **PDF Export:** Print-friendly report layouts
2. **Excel Export:** Raw data for further analysis
3. **CSV Export:** Integration with external systems
4. **Email Scheduling:** Automated report delivery
5. **Dashboard Widgets:** Key metrics on main dashboard

---

## Security & Access Control

### Permissions

-   Reports accessible to users with proper tenant access
-   Email and phone information visible based on user role
-   Financial data requires accounting module access

### Data Isolation

-   All queries filtered by `tenant_id`
-   Cross-tenant data access prevented at query level
-   Row-level security enforced

---

## Usage Examples

### Example 1: Monthly Performance Review

```
1. Navigate to Sales Summary Report
2. Set date range to previous month
3. Group by week
4. Compare with previous month
5. Review top customers and products
6. Export for management presentation
```

### Example 2: Customer Account Analysis

```
1. Open Customer Sales Report
2. Filter by specific customer
3. Review purchase history and patterns
4. Check last sale date for follow-up
5. Calculate customer lifetime value
```

### Example 3: Product Profitability Audit

```
1. Access Product Sales Report
2. Sort by profit margin (ascending)
3. Identify products with <10% margin
4. Review pricing and cost data
5. Make pricing adjustments
```

### Example 4: Seasonal Trend Analysis

```
1. Use Sales by Period Report
2. Set full year date range
3. Group by monthly
4. Compare with previous year
5. Identify seasonal patterns
6. Plan inventory and staffing
```

---

## Troubleshooting

### No Data Showing

-   Verify date range includes posted invoices
-   Check tenant has sales data in selected period
-   Ensure voucher types properly configured

### Incorrect Profit Calculations

-   Verify products have purchase_rate set
-   Check invoice items have accurate cost data
-   Review accounting entries for anomalies

### Performance Issues

-   Limit date range for initial queries
-   Use specific filters (customer, product, category)
-   Consider database indexing on voucher_date

---

## Future Enhancements

### Planned Features

1. **Graphical Charts:** Visual trend lines and pie charts
2. **Predictive Analytics:** Sales forecasting using historical data
3. **Custom Report Builder:** User-defined report parameters
4. **Real-time Dashboard:** Live sales monitoring
5. **Mobile Responsive:** Optimized for mobile devices
6. **Multi-currency Support:** Handle foreign exchange in reports
7. **Commission Calculations:** Sales team performance tracking
8. **Target vs Actual:** Goal tracking and variance analysis

---

## Related Documentation

-   Invoice Management System
-   Voucher Processing
-   Accounting Module
-   Product Management
-   Customer Relationship Management (CRM)

---

## Support

For issues or feature requests related to sales reports:

-   Technical Lead: Development Team
-   Business Analysis: Accounting Department
-   User Training: Support Team

---

**Last Updated:** October 23, 2025
**Version:** 1.0
**Module:** Sales Reports
**System:** Ballie ERP
