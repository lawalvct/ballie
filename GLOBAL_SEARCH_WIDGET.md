# Global Search Widget Implementation

## Overview

A floating search widget that allows users to quickly search and navigate throughout the tenant application. Similar to the Cmd+K pattern in modern applications like VS Code, Notion, and Slack.

## Features

### 1. **Floating Button**

-   Fixed position at bottom-right corner
-   Gradient purple-to-blue styling
-   Hover animations and shadow effects
-   Always accessible from any page

### 2. **Search Modal**

-   Full-screen overlay with backdrop
-   Centered search panel with modern design
-   Auto-focus on search input
-   Smooth animations on open/close

### 3. **Keyboard Shortcuts**

-   **Ctrl+K** (or **Cmd+K** on Mac) - Open search modal
-   **Escape** - Close search modal
-   **Arrow keys** - Navigate results (future enhancement)
-   **Enter** - Open selected result (future enhancement)

### 4. **Search Categories**

#### Quick Actions

-   Context-aware suggestions based on search query
-   Color-coded action buttons
-   Examples:
    -   "invoice" → Quick actions to create sales/purchase invoice
    -   "customer" → Quick actions to create customer, view list
    -   "product" → Quick actions to create product, manage inventory

#### Pages & Features (Routes)

-   30+ searchable routes across:
    -   **Accounting**: Vouchers, Ledgers, Journal Entries, Trial Balance
    -   **CRM**: Customers, Vendors, Contacts
    -   **Inventory**: Products, Stock Management, Transfers
    -   **POS**: Sales, Billing, Cash Register
    -   **Reports**: Financial Reports, Inventory Reports, Sales Reports
    -   **Settings**: Company Settings, User Management, Preferences
    -   **Dashboard**: Main Dashboard, Analytics

#### Records (Database)

-   Real-time search across:
    -   **Customers**: Name, Email, Phone
    -   **Products**: Name, SKU, Barcode
    -   **Vouchers**: Voucher Number, Narration
    -   **Ledger Accounts**: Name, Code

### 5. **Search UI Components**

#### Empty State

-   Displayed when search is inactive
-   Shows search icon and helpful hints
-   Sample search suggestions

#### Loading State

-   Animated spinner during search
-   "Searching..." message

#### No Results State

-   Friendly message when no matches found
-   Suggestion to try different keywords

#### Results Display

-   Organized into sections (Quick Actions, Routes, Records)
-   Each result shows:
    -   Icon with category-based color
    -   Title and description
    -   Category badge
    -   Hover effects with border highlight

### 6. **Debounced Search**

-   300ms delay after typing stops
-   Prevents excessive API calls
-   Minimum 2 characters required

## Technical Implementation

### Files Created

1. **`resources/views/components/global-search-widget.blade.php`**

    - Complete widget UI with modal and search functionality
    - Embedded CSS for animations
    - JavaScript for search logic, keyboard shortcuts, and result rendering

2. **`app/Http/Controllers/Tenant/Api/GlobalSearchController.php`**

    - `search()` - Main search endpoint
    - `searchRoutes()` - Filter predefined routes
    - `searchRecords()` - Search database models
    - `quickActions()` - Context-aware action suggestions

3. **`routes/tenant.php`** (Updated)

    - Added GlobalSearchController import
    - Added API routes:
        - `GET /api/global-search`
        - `GET /api/quick-actions`

4. **`resources/views/layouts/tenant.blade.php`** (Updated)
    - Included global search widget component

### API Endpoints

#### GET `/api/global-search?query={search_term}`

**Response:**

```json
{
    "routes": [
        {
            "title": "Create Sales Invoice",
            "description": "Create a new sales invoice",
            "url": "https://tenant.ballie.test/tenant/slug/accounting/vouchers/create?type=sales",
            "icon": "fas fa-file-invoice",
            "category": "Accounting"
        }
    ],
    "records": [
        {
            "title": "John Doe",
            "description": "john@example.com | +1234567890",
            "url": "https://tenant.ballie.test/tenant/slug/crm/customers/1",
            "type": "customer",
            "category": "Customer",
            "icon": "fas fa-user"
        }
    ]
}
```

#### GET `/api/quick-actions?query={search_term}`

**Response:**

```json
[
    {
        "title": "Create Sales Invoice",
        "url": "https://tenant.ballie.test/tenant/slug/accounting/vouchers/create?type=sales",
        "icon": "fas fa-plus-circle",
        "color": "blue"
    }
]
```

### Search Algorithm

1. **Route Search**: Case-insensitive substring matching on:

    - Route title
    - Route description
    - Route keywords

2. **Database Search**: SQL LIKE queries on:

    - Customers: name, email, phone
    - Products: name, sku, barcode
    - Vouchers: voucher_number, narration
    - Ledger Accounts: name, code

3. **Result Limits**:
    - Routes: Up to 10 matches
    - Records per model: Up to 5 matches

## Usage Guide

### For Users

1. **Open Search**:

    - Click the floating purple button at bottom-right
    - Or press **Ctrl+K** (Cmd+K on Mac)

2. **Search**:

    - Type at least 2 characters
    - See results appear in real-time
    - Results grouped by category

3. **Navigate**:
    - Click any result to navigate to that page
    - Use quick actions for common tasks
    - Press **Escape** to close

### Search Examples

-   **"sales invoice"** → Shows create invoice, view invoices, sales reports
-   **"john"** → Shows customers/vendors named John
-   **"product"** → Shows product pages, create product, inventory
-   **"report"** → Shows all available reports
-   **"INV-001"** → Shows voucher with that number
-   **"cash"** → Shows cash ledger account, cash reports

## Future Enhancements

### 1. **Keyboard Navigation**

-   Arrow Up/Down to select results
-   Enter to open selected result
-   Tab to switch between sections

### 2. **Recent Searches**

-   Store last 10 searches in localStorage
-   Show recent searches when modal opens
-   Clear history option

### 3. **Search History**

-   Track frequently searched items
-   Show "Frequently Used" section
-   Personalized suggestions

### 4. **AI-Powered Search** (OpenAI Integration)

-   Natural language queries: "Show me last month's sales"
-   Smart suggestions: "Create customer" → Pre-fills form fields
-   Context understanding: "Overdue invoices" → Filters automatically

### 5. **Global Command Palette**

-   Execute actions without navigation
-   "Create customer John Doe" → Opens create form with name
-   "Export sales report" → Triggers export

### 6. **Advanced Filters**

-   Date range filters
-   Status filters (pending, completed, etc.)
-   Amount range filters

### 7. **Search Analytics**

-   Track most searched items
-   Identify navigation patterns
-   Improve search relevance

## Customization

### Modify Searchable Routes

Edit `app/Http/Controllers/Tenant/Api/GlobalSearchController.php`:

```php
private function getSearchableRoutes()
{
    return [
        [
            'title' => 'Your Custom Page',
            'description' => 'Description here',
            'route' => 'tenant.your.route',
            'params' => [],
            'icon' => 'fas fa-icon-name',
            'category' => 'Category Name',
            'keywords' => ['keyword1', 'keyword2']
        ],
        // ... more routes
    ];
}
```

### Modify Search Models

Add new models to search in `searchRecords()` method:

```php
// Example: Add Invoice search
$invoices = \App\Models\Invoice::where('tenant_id', $tenantId)
    ->where(function($query) use ($query) {
        $query->where('invoice_number', 'like', "%{$query}%")
              ->orWhere('customer_name', 'like', "%{$query}%");
    })
    ->limit(5)
    ->get();

foreach ($invoices as $invoice) {
    $results[] = [
        'title' => "Invoice {$invoice->invoice_number}",
        'description' => $invoice->customer_name,
        'url' => route('tenant.invoices.show', ['tenant' => tenant()->slug, 'invoice' => $invoice->id]),
        'type' => 'invoice',
        'category' => 'Invoice',
        'icon' => 'fas fa-file-invoice-dollar'
    ];
}
```

### Modify Colors & Styling

Edit `resources/views/components/global-search-widget.blade.php`:

```javascript
// Change category colors
function getCategoryColor(category) {
    const colors = {
        Accounting: "bg-blue-500", // Change this
        CRM: "bg-green-500", // Change this
        // ... etc
    };
    return colors[category] || "bg-gray-500";
}
```

### Change Floating Button Position

Edit the component's first div:

```html
<!-- Move to bottom-left -->
<div id="globalSearchWidget" class="fixed bottom-6 left-6 z-50">
    <!-- Move to top-right -->
    <div id="globalSearchWidget" class="fixed top-6 right-6 z-50"></div>
</div>
```

## Troubleshooting

### Search Not Working

1. Check browser console for errors
2. Verify routes exist: `php artisan route:list | grep global-search`
3. Check tenant slug is correct in URL
4. Verify middleware allows access

### No Results Showing

1. Check if query is at least 2 characters
2. Verify database records exist
3. Check tenant_id filtering in queries
4. Review browser network tab for API response

### Keyboard Shortcut Not Working

1. Check if another extension/app uses Ctrl+K
2. Try clicking the floating button instead
3. Check browser console for JavaScript errors

### Styling Issues

1. Ensure Tailwind CSS is compiled: `npm run dev`
2. Check for CSS conflicts with existing styles
3. Verify Font Awesome is loaded

## Performance Considerations

-   **Debounced Search**: 300ms delay prevents API spam
-   **Result Limits**: Max 10 routes + 5 records per model
-   **Database Indexing**: Ensure indexed columns for search fields
-   **Caching**: Consider caching route list (rarely changes)

## Security

-   ✅ Tenant isolation enforced via middleware
-   ✅ SQL injection prevented via Eloquent query builder
-   ✅ XSS protection via Laravel's Blade escaping
-   ✅ CSRF protection on all routes (GET requests exempt)
-   ✅ Authorization checks in route definitions

## Testing

### Manual Testing

1. Open tenant dashboard
2. Press Ctrl+K
3. Search for "sales"
4. Verify results appear
5. Click a result
6. Verify navigation works

### Test Cases

-   Search with 1 character (should show empty state)
-   Search with 2+ characters (should show results)
-   Search for nonexistent term (should show no results)
-   Press Escape (should close modal)
-   Click backdrop (should close modal)
-   Press Ctrl+K twice (should toggle modal)

## Browser Compatibility

-   ✅ Chrome 90+
-   ✅ Firefox 88+
-   ✅ Safari 14+
-   ✅ Edge 90+

## Mobile Support

The search widget is fully responsive:

-   Floating button sized appropriately
-   Modal adjusts to screen size
-   Touch-friendly tap targets
-   Optimized for mobile keyboards

---

**Last Updated**: January 19, 2025
**Version**: 1.0.0
**Author**: Ballie Development Team
