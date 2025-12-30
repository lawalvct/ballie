# Voucher Management API - Complete Summary

## ✅ Implementation Status: COMPLETE

All endpoints are fully functional and ready for React Native consumption.

---

## 📋 Available Endpoints

### Base URL

```
https://yourdomain.com/api/v1/tenant/{tenant_slug}/accounting/vouchers
```

### Authentication

All endpoints require Bearer token:

```
Authorization: Bearer {your_token}
```

---

## 1️⃣ GET /create - Get Form Data

### Purpose

Load all necessary data for creating a new voucher in your React Native app.

### Request

```http
GET /api/v1/tenant/{tenant}/accounting/vouchers/create?type=jv
```

### Query Parameters

-   `type` (optional): Pre-select voucher type
    -   `jv` - Journal Voucher
    -   `pv` - Payment Voucher
    -   `rv` - Receipt Voucher
    -   `cv` - Contra Voucher
    -   `cn` - Credit Note
    -   `dn` - Debit Note

### Response Structure

```json
{
    "success": true,
    "data": {
        "voucher_types": [
            {
                "id": 1,
                "name": "Journal Voucher",
                "code": "JV",
                "description": "General journal entries",
                "has_numbering": true,
                "number_prefix": "JV-",
                "number_suffix": "",
                "next_number": 1001
            }
        ],
        "ledger_accounts": [
            {
                "id": 1,
                "name": "Cash in Hand",
                "code": "1001",
                "display_name": "Cash in Hand (1001)",
                "account_type": "asset",
                "account_group_id": 1,
                "account_group_name": "Current Assets",
                "parent_id": null,
                "level": 0,
                "current_balance": 50000.0
            }
        ],
        "products": [
            {
                "id": 1,
                "name": "Product A",
                "sku": "PRD-001",
                "price": 100.0,
                "cost": 60.0,
                "stock_quantity": 50
            }
        ],
        "selected_type": {
            "id": 1,
            "name": "Journal Voucher",
            "code": "JV",
            "description": "General journal entries"
        },
        "defaults": {
            "voucher_date": "2025-12-30",
            "status": "draft"
        },
        "validation_rules": {
            "voucher_type_id": "required|exists:voucher_types,id",
            "voucher_date": "required|date",
            "voucher_number": "nullable|string|max:50",
            "narration": "nullable|string|max:1000",
            "entries": "required|array|min:2",
            "entries.*.ledger_account_id": "required|exists:ledger_accounts,id",
            "entries.*.debit_amount": "nullable|numeric|min:0",
            "entries.*.credit_amount": "nullable|numeric|min:0",
            "entries.*.description": "nullable|string|max:500"
        }
    },
    "message": "Form data retrieved successfully"
}
```

### What You Get

#### 1. **Voucher Types** (All Active)

-   Complete list of all available voucher types for your tenant
-   Includes numbering settings (prefix, suffix, next number)
-   Use this to populate voucher type dropdown/selector

#### 2. **Ledger Accounts** (All Active)

-   Complete list of all active ledger accounts
-   Includes current balance for each account
-   Organized with account group information
-   Use this to populate account pickers in entry rows

#### 3. **Products** (All Active)

-   List of all products (for inventory-related entries)
-   Includes pricing and stock information
-   Optional - use if needed for product-based entries

#### 4. **Selected Type** (If type parameter provided)

-   Pre-selected voucher type details
-   Useful when navigating from quick-create buttons

#### 5. **Defaults**

-   Current date pre-filled
-   Default status set to "draft"

#### 6. **Validation Rules**

-   Complete validation rules for client-side validation
-   Helps you implement proper form validation before API submission

---

## 2️⃣ POST / - Create Voucher

### Request

```http
POST /api/v1/tenant/{tenant}/accounting/vouchers
Content-Type: application/json
```

### Request Body

```json
{
    "voucher_type_id": 1,
    "voucher_date": "2025-12-30",
    "voucher_number": "JV-1001",
    "narration": "Opening balance entry",
    "reference_number": "REF-2025-001",
    "entries": [
        {
            "ledger_account_id": 1,
            "debit_amount": 50000,
            "credit_amount": 0,
            "description": "Cash received"
        },
        {
            "ledger_account_id": 10,
            "debit_amount": 0,
            "credit_amount": 50000,
            "description": "Capital"
        }
    ],
    "action": "save"
}
```

### Action Options

-   `save` - Save as draft (default)
-   `save_and_post` - Save and post immediately

---

## 3️⃣ GET / - List Vouchers

### Request

```http
GET /api/v1/tenant/{tenant}/accounting/vouchers?per_page=20&search=JV&status=draft
```

### Query Parameters

| Parameter         | Type   | Description                                    |
| ----------------- | ------ | ---------------------------------------------- |
| `per_page`        | number | Items per page (default: 20)                   |
| `search`          | string | Search in voucher number, narration, reference |
| `voucher_type_id` | number | Filter by voucher type                         |
| `status`          | string | Filter by status (draft, posted)               |
| `date_from`       | date   | Start date (YYYY-MM-DD)                        |
| `date_to`         | date   | End date (YYYY-MM-DD)                          |
| `sort_by`         | string | Sort field (default: voucher_date)             |
| `sort_direction`  | string | Sort direction (asc, desc)                     |

### Response Includes

-   Paginated voucher list
-   Statistics (total, draft, posted counts)
-   Pagination metadata

---

## 4️⃣ GET /{id} - Get Voucher Details

### Request

```http
GET /api/v1/tenant/{tenant}/accounting/vouchers/1
```

### Response Includes

-   Complete voucher information
-   All entries with account details
-   Created by, updated by, posted by users
-   Action permissions (can_be_edited, can_be_deleted, etc.)

---

## 5️⃣ PUT /{id} - Update Voucher

### Constraints

-   Only **draft** vouchers can be updated
-   Must be balanced (total debits = total credits)
-   Requires same validation as create

---

## 6️⃣ DELETE /{id} - Delete Voucher

### Constraints

-   Only **draft** vouchers can be deleted
-   All entries are automatically deleted

---

## 7️⃣ POST /{id}/post - Post Voucher

### What Happens

-   Status changes to "posted"
-   Becomes read-only
-   posted_at timestamp is set
-   posted_by user is recorded

### Constraints

-   Must be in **draft** status
-   Must be balanced
-   Must have at least 2 entries

---

## 8️⃣ POST /{id}/unpost - Unpost Voucher

### What Happens

-   Status reverts to "draft"
-   Becomes editable again
-   posted_at and posted_by are cleared

---

## 9️⃣ GET /{id}/duplicate - Get Duplicate Data

### Purpose

Get pre-filled data to create a duplicate voucher

### Response

-   Same structure as original voucher
-   Date set to current date
-   Reference number cleared
-   Ready to use in create form

---

## 🔟 POST /bulk-action - Bulk Operations

### Request

```json
{
    "action": "post",
    "voucher_ids": [1, 2, 3, 4, 5]
}
```

### Actions

-   `post` - Post multiple draft vouchers
-   `unpost` - Unpost multiple posted vouchers
-   `delete` - Delete multiple draft vouchers

### Response

```json
{
    "success": true,
    "message": "5 vouchers processed successfully",
    "data": {
        "success_count": 4,
        "failed_count": 1,
        "errors": ["Voucher PV-2005 is posted and cannot be deleted"]
    }
}
```

---

## 1️⃣1️⃣ GET /search - Search Vouchers

### Purpose

Quick search for autocomplete/search bar

### Request

```http
GET /api/v1/tenant/{tenant}/accounting/vouchers/search?q=JV&status=posted
```

### Returns

-   Maximum 20 results
-   Simplified data with display_name
-   Sorted by date (newest first)

---

## 🎯 React Native Implementation

### Step 1: Load Form Data on Screen Mount

```typescript
useEffect(() => {
    const loadFormData = async () => {
        try {
            const response = await voucherService.getCreateData();

            // Set voucher types for dropdown
            setVoucherTypes(response.data.voucher_types);

            // Set ledger accounts for entry rows
            setLedgerAccounts(response.data.ledger_accounts);

            // Set products if needed
            setProducts(response.data.products);

            // Set default date
            setFormData({
                voucher_date: response.data.defaults.voucher_date,
                status: response.data.defaults.status,
            });
        } catch (error) {
            console.error("Failed to load form data:", error);
        }
    };

    loadFormData();
}, []);
```

### Step 2: Render Voucher Type Selector

```typescript
<Picker
    selectedValue={formData.voucher_type_id}
    onValueChange={(value) =>
        setFormData({ ...formData, voucher_type_id: value })
    }
>
    <Picker.Item label="Select Voucher Type" value={null} />
    {voucherTypes.map((type) => (
        <Picker.Item
            key={type.id}
            label={`${type.name} (${type.code})`}
            value={type.id}
        />
    ))}
</Picker>
```

### Step 3: Render Ledger Account Picker (for each entry)

```typescript
<Picker
    selectedValue={entry.ledger_account_id}
    onValueChange={(value) => updateEntry(entry.id, "ledger_account_id", value)}
>
    <Picker.Item label="Select Account" value={null} />
    {ledgerAccounts.map((account) => (
        <Picker.Item
            key={account.id}
            label={account.display_name}
            value={account.id}
        />
    ))}
</Picker>
```

### Step 4: Show Account Balance

```typescript
const selectedAccount = ledgerAccounts.find(
    (acc) => acc.id === entry.ledger_account_id
);

{
    selectedAccount && (
        <Text style={styles.balanceText}>
            Current Balance: ₹{selectedAccount.current_balance.toFixed(2)}
        </Text>
    );
}
```

---

## ✅ What's Already Complete

1. ✅ **All 11 API Endpoints** - Fully functional
2. ✅ **Voucher Types Loading** - Returns all active voucher types
3. ✅ **Ledger Accounts Loading** - Returns all active ledger accounts with balances
4. ✅ **Products Loading** - Returns all active products
5. ✅ **Validation Rules** - Complete rules for client-side validation
6. ✅ **Default Values** - Current date and draft status
7. ✅ **Selected Type** - Optional pre-selection via query parameter
8. ✅ **Error Handling** - Comprehensive error messages
9. ✅ **Route Registration** - All routes registered in api/v1/tenant.php
10. ✅ **Postman Collection** - Complete with examples

---

## 📚 Files Available

1. **VoucherController.php** - Complete API controller

    - Location: `app/Http/Controllers/Api/Tenant/Accounting/VoucherController.php`

2. **Routes** - All routes registered

    - Location: `routes/api/v1/tenant.php`

3. **Postman Collection** - Complete API documentation

    - Location: `Ballie_Vouchers_API.postman_collection.json`

4. **Implementation Guide** - React Native guide
    - Location: `VOUCHER_MANAGEMENT_REACT_NATIVE_GUIDE.md`

---

## 🚀 Ready to Use

Everything is already implemented and ready for your React Native app to consume!

### Quick Start Checklist

-   ✅ Routes cleared: `php artisan route:clear`
-   ✅ All voucher types will load automatically
-   ✅ All ledger accounts will load automatically
-   ✅ Current balances included for each account
-   ✅ Validation rules provided
-   ✅ Error handling implemented
-   ✅ Postman collection ready for testing

### Testing the Endpoint

```bash
# Test in terminal (replace with your actual URL and token)
curl -X GET "http://your-domain.com/api/v1/tenant/your-tenant/accounting/vouchers/create" \
  -H "Authorization: Bearer your-token-here" \
  -H "Accept: application/json"
```

---

## 🎨 UI Components Needed

Based on the data structure, you'll need these React Native components:

1. **VoucherTypeSelector** - Dropdown/Modal for selecting voucher type
2. **LedgerAccountPicker** - Searchable dropdown for selecting accounts
3. **EntryRow** - Component for each voucher entry
4. **DatePicker** - For voucher date selection
5. **TextInput** - For narration and reference number
6. **TotalsSummary** - Shows debit/credit totals and balance status

All component examples are in the implementation guide!

---

## 💡 Key Points

### 1. Data Already Loads

The `/create` endpoint already loads:

-   ✅ All tenant voucher types
-   ✅ All tenant ledger accounts (with current balances)
-   ✅ All tenant products
-   ✅ Default values
-   ✅ Validation rules

### 2. No Additional Changes Needed

The implementation is complete. Just call the endpoint from your React Native app.

### 3. Current Balance Included

Each ledger account includes `current_balance` field - perfect for showing users account balances when selecting accounts.

### 4. Smart Account Selection

Accounts include:

-   `display_name` - Formatted name with code
-   `account_group_name` - For grouping/filtering
-   `account_type` - For type-based filtering
-   `current_balance` - Real-time balance

---

## 🎯 Next Steps

1. **Import Postman Collection** - Test all endpoints
2. **Review Implementation Guide** - See React Native examples
3. **Start Building Screens** - Use the provided component examples
4. **Test with Real Data** - Create test vouchers through the API

Everything you need is ready and documented! 🚀
