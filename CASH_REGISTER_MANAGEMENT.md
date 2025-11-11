# Cash Register Management System - Quick Reference

## Overview

Complete cash register management system for POS operations, allowing admins to create, edit, and manage physical register points.

## Implementation Summary

### ✅ Controller

**File**: `app/Http/Controllers/Tenant/Settings/CashRegisterController.php`

**Methods**:

-   `index()` - List all cash registers with statistics
-   `create()` - Show create form
-   `store()` - Validate and save new register
-   `edit()` - Show edit form with warnings for active sessions
-   `update()` - Update register details (name, location, status)
-   `destroy()` - Delete register (only if no sessions/sales)
-   `toggleStatus()` - Activate/deactivate register

**Validation Rules**:

```php
'name' => 'required|string|max:255|unique:cash_registers,name,{id},id,tenant_id,{tenant_id}',
'location' => 'nullable|string|max:500',
'opening_balance' => 'required|numeric|min:0', // Only on create
'is_active' => 'boolean',
```

**Safety Features**:

-   Cannot delete registers with existing sessions or sales
-   Cannot deactivate registers with open sessions
-   Tenant isolation enforced
-   Audit tracking (created_by, updated_by)

---

### ✅ Routes

**File**: `routes/tenant.php` (Lines 948-956)

**Location**: Inside `Route::prefix('settings')` group

**Routes**:

```php
tenant.settings.cash-registers.index         GET     /settings/cash-registers
tenant.settings.cash-registers.create        GET     /settings/cash-registers/create
tenant.settings.cash-registers.store         POST    /settings/cash-registers
tenant.settings.cash-registers.edit          GET     /settings/cash-registers/{cashRegister}/edit
tenant.settings.cash-registers.update        PUT     /settings/cash-registers/{cashRegister}
tenant.settings.cash-registers.destroy       DELETE  /settings/cash-registers/{cashRegister}
tenant.settings.cash-registers.toggle-status PATCH   /settings/cash-registers/{cashRegister}/toggle-status
```

---

### ✅ Views

#### Index View

**File**: `resources/views/tenant/settings/cash-registers/index.blade.php`

**Features**:

-   Statistics dashboard (Total, Active, Inactive, Total Sessions)
-   Register table with:
    -   Name & Location
    -   Opening Balance & Current Balance
    -   Session count & Sales count
    -   Status badge (Active/Inactive)
    -   Actions: Edit, Toggle Status, Delete
-   Delete button disabled for registers with history
-   Empty state with call-to-action
-   Information cards explaining system behavior

#### Create View

**File**: `resources/views/tenant/settings/cash-registers/create.blade.php`

**Features**:

-   Uses shared form partial
-   Tips card for setting up registers
-   Cancel/Create buttons

#### Edit View

**File**: `resources/views/tenant/settings/cash-registers/edit.blade.php`

**Features**:

-   Warning alert if register has active sessions
-   Statistics card showing:
    -   Total Sessions, Active Sessions, Total Sales
    -   Opening Balance (read-only)
    -   Current Balance (read-only, auto-managed)
-   Delete button at bottom (disabled if has history)
-   Important notes card explaining editing restrictions

#### Form Partial

**File**: `resources/views/tenant/settings/cash-registers/_form.blade.php`

**Fields**:

-   **Register Name** (required, max 255 chars, unique per tenant)
    -   Examples: "Counter 1", "Drive-through", "Express Lane"
-   **Location** (optional, max 500 chars)
    -   Examples: "Main Floor", "Second Floor", "Front Desk"
-   **Opening Balance** (required on create, read-only on edit)
    -   Min: 0, Step: 0.01, Currency formatted
    -   Cannot be changed after creation
-   **Current Balance** (display only on edit)
    -   Automatically updated by system
-   **Active Status** (checkbox)
    -   Only active registers appear in POS session dropdown
-   Help text with quick guide

---

### ✅ Navigation

**File**: `resources/views/layouts/tenant/sidebar.blade.php`

**Menu Item Added**: After "Settings", before closing `</ul>`

**Details**:

-   Label: "Cash Registers"
-   Icon: Currency/dollar sign circle (purple-400)
-   Route: `tenant.settings.cash-registers.index`
-   Active when: `request()->routeIs('tenant.settings.cash-registers.*')`
-   Title: "Cash Register Management"

**Settings Menu Updated**:

-   Modified active state to exclude cash-registers routes
-   Prevents both menus highlighting simultaneously

---

## Usage Guide

### For Admins: Adding New Registers

1. **Navigate**: Click "Cash Registers" in sidebar
2. **Create**: Click "Add New Register" button
3. **Fill Form**:
    - Enter descriptive name (e.g., "Counter 1")
    - Optionally add location (e.g., "Main Floor")
    - Set opening balance (usually 0)
    - Check "Active" to make immediately available
4. **Save**: Click "Create Cash Register"

### For Admins: Editing Registers

1. **Navigate**: Cash Registers → Click edit icon (✏️)
2. **Modify**: Update name, location, or active status
3. **Cannot Edit**: Opening balance, current balance (system-managed)
4. **Warning**: Active sessions shown at top if any exist
5. **Save**: Click "Update Cash Register"

### For Admins: Deactivating Registers

**Option 1 - Toggle from List**:

-   Click ban icon (🚫) next to register in table
-   Confirm action

**Option 2 - From Edit Page**:

-   Uncheck "Active" checkbox
-   Click "Update Cash Register"

**Requirement**: No open sessions on the register

### For Admins: Deleting Registers

**From List or Edit Page**:

-   Click delete/trash icon (🗑️)
-   Confirm deletion

**Requirements**:

-   No sessions exist (even closed ones)
-   No sales exist
-   Otherwise, button is disabled

**Alternative**: Deactivate instead of deleting

---

## System Behavior

### Opening Balance vs Current Balance

**Opening Balance**:

-   Set once during register creation
-   Represents initial cash float
-   Cannot be changed after creation
-   Typically set to 0

**Current Balance**:

-   Automatically managed by system
-   Updates when sessions are opened/closed
-   Reflects current cash in register
-   Display only (cannot be manually edited)

### Active vs Inactive Registers

**Active Registers**:

-   Appear in POS session dropdown
-   Can accept new session openings
-   Show with green "Active" badge
-   Can be toggled to inactive (if no open sessions)

**Inactive Registers**:

-   Hidden from POS session dropdown
-   Cannot accept new sessions
-   Show with gray "Inactive" badge
-   Can be reactivated anytime
-   Retain all session/sales history

### Session Restrictions

**Cannot Deactivate**:

-   If register has open sessions
-   Must close all sessions first

**Cannot Delete**:

-   If register has any sessions (open or closed)
-   If register has any sales
-   Deactivate instead

---

## Integration Points

### POS Session Opening

**File**: `resources/views/tenant/pos/register-session.blade.php`

**Behavior**:

-   Dropdown shows only active registers
-   Format: "Register Name - Location"
-   After adding new register through settings, it immediately appears in dropdown

### Database Structure

**Table**: `cash_registers`

**Columns**:

```
id               - Primary key
tenant_id        - Foreign key to tenants table
name             - Register name (unique per tenant)
location         - Physical location (nullable)
opening_balance  - Initial cash float (decimal 15,2)
current_balance  - Current cash amount (decimal 15,2)
is_active        - Boolean active status
created_by       - User who created (nullable)
updated_by       - User who last updated (nullable)
created_at       - Timestamp
updated_at       - Timestamp
```

**Relationships**:

-   `hasMany` → CashRegisterSession
-   `hasMany` → Sale
-   `belongsTo` → Tenant

---

## Testing Checklist

### Create Register

-   [ ] Form validation works (required fields)
-   [ ] Unique name enforced per tenant
-   [ ] Opening balance accepts decimals
-   [ ] Register appears in POS dropdown
-   [ ] Statistics update on index page

### Edit Register

-   [ ] Name can be updated (within uniqueness constraint)
-   [ ] Location can be updated
-   [ ] Status can be toggled
-   [ ] Opening balance is read-only
-   [ ] Current balance is read-only
-   [ ] Active session warning appears if applicable

### Delete Register

-   [ ] Can delete unused registers
-   [ ] Cannot delete registers with sessions
-   [ ] Cannot delete registers with sales
-   [ ] Button disabled appropriately
-   [ ] Confirmation dialog appears

### Toggle Status

-   [ ] Can activate inactive registers
-   [ ] Can deactivate registers without open sessions
-   [ ] Cannot deactivate registers with open sessions
-   [ ] Error message appears when restricted
-   [ ] Status badge updates on list

### Integration

-   [ ] New registers appear in POS session dropdown
-   [ ] Inactive registers hidden from POS dropdown
-   [ ] Sessions link correctly to registers
-   [ ] Tenant isolation verified

---

## Common Admin Tasks

### Setting Up Multiple Locations

```
1. Navigate to Cash Registers
2. Add registers one by one:
   - "Front Counter" (Main Floor)
   - "Drive-through" (Exterior)
   - "Express Lane" (Main Floor)
   - "Customer Service" (Second Floor)
3. Set opening balances (typically 0)
4. All marked as Active
```

### Temporary Register Closure

```
1. Navigate to Cash Registers
2. Click edit on register to close
3. Verify no active sessions (close them first if any)
4. Uncheck "Active"
5. Save
Result: Register hidden from POS but data retained
```

### Removing Unused Register

```
1. Navigate to Cash Registers
2. Check sessions_count and sales_count columns
3. If both are 0, delete button enabled
4. Click delete, confirm
5. Register permanently removed
```

---

## Error Messages

**Cannot Delete**:

> "Cannot delete cash register with existing sessions. Deactivate it instead."
> "Cannot delete cash register with existing sales. Deactivate it instead."

**Cannot Deactivate**:

> "Cannot deactivate cash register with active sessions. Close all sessions first."

**Validation Errors**:

> "The name has already been taken." (duplicate name)
> "The name field is required."
> "The opening balance field is required."
> "The opening balance must be at least 0."

---

## Files Modified/Created

### Created (5 files):

1. `app/Http/Controllers/Tenant/Settings/CashRegisterController.php`
2. `resources/views/tenant/settings/cash-registers/index.blade.php`
3. `resources/views/tenant/settings/cash-registers/create.blade.php`
4. `resources/views/tenant/settings/cash-registers/edit.blade.php`
5. `resources/views/tenant/settings/cash-registers/_form.blade.php`

### Modified (2 files):

1. `routes/tenant.php` (Added 7 routes in settings group)
2. `resources/views/layouts/tenant/sidebar.blade.php` (Added menu item, updated Settings active state)

---

## Quick Access

**Admin Panel**: `/{tenant}/settings/cash-registers`
**Create**: `/{tenant}/settings/cash-registers/create`
**Edit**: `/{tenant}/settings/cash-registers/{id}/edit`

**Navigation**: Sidebar → Cash Registers (purple icon)

---

## Summary

✅ **Complete CRUD system for cash registers**
✅ **Safety checks prevent data loss**
✅ **Tenant-isolated and audit-tracked**
✅ **Integrated with POS system**
✅ **User-friendly interface with statistics**
✅ **Professional validation and error handling**

Admins can now add, edit, and manage register points entirely through the UI without database access!
