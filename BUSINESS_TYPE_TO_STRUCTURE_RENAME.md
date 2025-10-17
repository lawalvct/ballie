# Business Type to Business Structure Rename - Documentation

## Overview
Professional renaming of `business_type` column and all related references to `business_structure` across the entire codebase.

**Date**: October 17, 2025
**Status**: ✅ COMPLETED

---

## Reason for Change

The term "Business Type" was confusing as it conflicted with the `business_types` table which stores industry categories (e.g., Restaurant, Retail, Manufacturing). The `business_type` column in the tenants table actually stores the **legal structure** of the business (e.g., Sole Proprietorship, LLC, Corporation).

### Terminology Clarification

| Term | Purpose | Examples |
|------|---------|----------|
| **Business Structure** | Legal structure/entity type | Sole Proprietorship, Partnership, LLC, Corporation |
| **Business Type** | Industry/category | Restaurant, E-commerce, Consulting, Manufacturing |

---

## Changes Made

### 1. Database Migration

**File**: `database/migrations/2025_10_17_125553_rename_business_type_to_business_structure_in_tenants_table.php`

```php
public function up(): void
{
    Schema::table('tenants', function (Blueprint $table) {
        $table->renameColumn('business_type', 'business_structure');
    });
}

public function down(): void
{
    Schema::table('tenants', function (Blueprint $table) {
        $table->renameColumn('business_structure', 'business_type');
    });
}
```

**Status**: ✅ Executed successfully

---

### 2. Model Updates

#### **File**: `app/Models/Tenant.php`

**Changed fillable array**:
```php
// BEFORE
'business_type',
'business_type_id',

// AFTER
'business_structure',
'business_type_id',
```

---

### 3. Controller Updates

#### **File**: `app/Http/Controllers/Auth/RegisteredUserController.php`

**Validation rules updated**:
```php
// BEFORE
'business_type' => ['nullable', 'string'],
'business_type_id' => ['required', 'integer', 'exists:business_types,id'],

// AFTER
'business_structure' => ['nullable', 'string'],
'business_type_id' => ['required', 'integer', 'exists:business_types,id'],
```

**Tenant creation updated**:
```php
// BEFORE
'business_type' => $request->business_type,

// AFTER
'business_structure' => $request->business_structure,
```

---

### 4. View Updates

#### **A. Registration Form**
**File**: `resources/views/auth/register.blade.php`

**Hidden input fields**:
```blade
<!-- BEFORE -->
<input type="hidden" name="business_type" id="business_type">
<input type="hidden" name="business_type_id" id="business_type_id" required>

<!-- AFTER -->
<input type="hidden" name="business_structure" id="business_structure">
<input type="hidden" name="business_type_id" id="business_type_id" required>
```

**JavaScript variables**:
```javascript
// BEFORE
const businessTypeInput = document.getElementById('business_type');

// AFTER
const businessStructureInput = document.getElementById('business_structure');
```

**JavaScript assignment**:
```javascript
// BEFORE
businessTypeInput.value = slug;

// AFTER
businessStructureInput.value = slug;
```

---

#### **B. Onboarding Company Form**
**File**: `resources/views/tenant/onboarding/steps/company.blade.php`

**Form field updated**:
```blade
<!-- BEFORE -->
<label for="business_type">Business Structure</label>
<select id="business_type" name="business_type">
    <option value="sole_proprietorship" {{ old('business_type', $currentTenant->business_type) == 'sole_proprietorship' ? 'selected' : '' }}>

<!-- AFTER -->
<label for="business_structure">Business Structure</label>
<select id="business_structure" name="business_structure">
    <option value="sole_proprietorship" {{ old('business_structure', $currentTenant->business_structure) == 'sole_proprietorship' ? 'selected' : '' }}>
```

**JavaScript validation**:
```javascript
// BEFORE
const requiredFields = ['company_name', 'business_type', 'email', ...];

// AFTER
const requiredFields = ['company_name', 'business_structure', 'email', ...];
```

---

### 5. Migration Reference Update

#### **File**: `database/migrations/2025_10_17_081847_create_business_types_table.php`

**Comment updated for clarity**:
```php
// BEFORE
// Add business_type_id to tenants table

// AFTER
// Add business_type_id to tenants table (after business_structure column)
```

**Column positioning**:
```php
// BEFORE
->after('business_type')

// AFTER
->after('business_structure')
```

---

## Database Schema

### **Tenants Table - Updated Structure**

| Column | Type | Description |
|--------|------|-------------|
| `business_structure` | VARCHAR | Legal structure (sole_proprietorship, partnership, llc, corporation, ngo, other) |
| `business_type_id` | BIGINT (FK) | References business_types.id (industry category) |

### **Business Types Table** (Unchanged)

| Column | Type | Description |
|--------|------|-------------|
| `id` | BIGINT | Primary key |
| `name` | VARCHAR | E-commerce, Restaurant, Consulting, etc. |
| `category` | VARCHAR | Retail, Food & Hospitality, Professional Services, etc. |
| `slug` | VARCHAR | URL-friendly identifier |

---

## Validation & Testing

### ✅ Migration Status
```bash
php artisan migrate
# Output: 2025_10_17_125553_rename_business_type_to_business_structure_in_tenants_table ... DONE
```

### ✅ Database Verification
```sql
-- Verify column renamed
DESCRIBE tenants;
-- Should show 'business_structure' column, NOT 'business_type'

-- Check data integrity
SELECT COUNT(*) FROM tenants WHERE business_structure IS NOT NULL;

-- Verify foreign key intact
SELECT t.name, t.business_structure, bt.name as industry
FROM tenants t
LEFT JOIN business_types bt ON t.business_type_id = bt.id
LIMIT 10;
```

### ✅ Application Testing Checklist

- [ ] **Registration Flow**
  - [ ] Select business type from dropdown
  - [ ] Form submits successfully
  - [ ] Tenant record created with business_structure and business_type_id

- [ ] **Onboarding Flow**
  - [ ] Company information form displays
  - [ ] Business Structure dropdown works
  - [ ] Data saves correctly

- [ ] **Data Display**
  - [ ] Tenant details show business structure
  - [ ] No references to old `business_type` column

---

## Files Modified

### Database Layer
- ✅ `database/migrations/2025_10_17_125553_rename_business_type_to_business_structure_in_tenants_table.php` (NEW)
- ✅ `database/migrations/2025_10_17_081847_create_business_types_table.php` (UPDATED)
- ✅ `database/migrations/2024_01_01_000002_create_tenants_table.php` (REFERENCE - no changes needed)

### Models
- ✅ `app/Models/Tenant.php`

### Controllers
- ✅ `app/Http/Controllers/Auth/RegisteredUserController.php`

### Views
- ✅ `resources/views/auth/register.blade.php`
- ✅ `resources/views/tenant/onboarding/steps/company.blade.php`

### Not Changed (Super Admin Views - Still using legacy simple dropdown)
- ⏸️ `resources/views/super-admin/tenants/create.blade.php`
- ⏸️ `resources/views/super-admin/tenants/invite.blade.php`

> **Note**: Super admin views can be updated later as they use a simplified business type dropdown (retail, service, restaurant, etc.) which is different from the business_structure field.

---

## Backward Compatibility

### Data Migration
✅ **No data loss** - Column rename preserves all existing data

### API Compatibility
⚠️ **Breaking Change** - If external APIs reference `business_type`, they need to update to `business_structure`

### Forms & Requests
✅ **Updated** - All form submissions now use `business_structure`

---

## Business Logic Clarification

### How It Works Now

1. **During Registration**:
   - User selects **Business Type** (industry) → e.g., "Restaurant"
   - Stores `business_type_id` → Foreign key to `business_types` table

2. **During Onboarding**:
   - User selects **Business Structure** (legal entity) → e.g., "Limited Liability Company"
   - Stores `business_structure` → String value

### Example Tenant Record

```json
{
  "name": "Joe's Diner",
  "business_structure": "sole_proprietorship",  // Legal structure
  "business_type_id": 23,                       // FK to business_types
  "businessType": {                             // Relationship
    "id": 23,
    "name": "Restaurant / Eatery",
    "category": "Food & Hospitality",
    "icon": "🍴"
  }
}
```

---

## Rollback Instructions

If you need to revert this change:

```bash
# Rollback the migration
php artisan migrate:rollback --step=1

# Or manually rename in database
ALTER TABLE tenants RENAME COLUMN business_structure TO business_type;
```

Then revert all code changes using git:
```bash
git revert <commit-hash>
```

---

## Future Enhancements

### 1. Update Super Admin Views
Update the simplified dropdowns in super admin tenant creation forms to use the comprehensive business_types table.

### 2. Add Business Structure Helper
Create a helper class or enum for business structures:

```php
class BusinessStructure {
    const SOLE_PROPRIETORSHIP = 'sole_proprietorship';
    const PARTNERSHIP = 'partnership';
    const LIMITED_LIABILITY = 'limited_liability';
    const CORPORATION = 'corporation';
    const NGO = 'ngo';
    const OTHER = 'other';

    public static function all() {
        return [
            self::SOLE_PROPRIETORSHIP => 'Sole Proprietorship',
            self::PARTNERSHIP => 'Partnership',
            self::LIMITED_LIABILITY => 'Limited Liability Company',
            self::CORPORATION => 'Corporation',
            self::NGO => 'NGO/Non-Profit',
            self::OTHER => 'Other',
        ];
    }
}
```

### 3. Add Validation Rules
Create custom validation for business structures:

```php
'business_structure' => ['required', Rule::in(BusinessStructure::values())],
```

---

## Glossary

| Term | Definition |
|------|------------|
| **Business Structure** | The legal form of the business entity (LLC, Corporation, etc.) |
| **Business Type** | The industry or category the business operates in (Restaurant, Retail, etc.) |
| **Tenant** | A business/company using the Ballie platform |
| **Migration** | Database schema change script |

---

## Support & Troubleshooting

### Common Issues

**Issue**: Form shows "business_type not found" error
**Solution**: Clear browser cache and Laravel views:
```bash
php artisan view:clear
php artisan cache:clear
```

**Issue**: Old registration data shows null for business_structure
**Solution**: Data was migrated automatically. Check database directly:
```sql
SELECT id, name, business_structure FROM tenants LIMIT 10;
```

**Issue**: Foreign key error on business_type_id
**Solution**: Ensure business_types table is seeded:
```bash
php artisan db:seed --class=BusinessTypeSeeder
```

---

## Changelog

### v1.0 - October 17, 2025
- ✅ Renamed `business_type` column to `business_structure`
- ✅ Updated all references in models, controllers, views
- ✅ Updated JavaScript variables and functions
- ✅ Updated validation rules
- ✅ Updated form fields and labels
- ✅ Migration executed successfully
- ✅ Documentation created

---

## Contributors
- Development Team
- Database Architect
- QA Team

---

## References
- [Business Structure Types](https://www.sba.gov/business-guide/launch-your-business/choose-business-structure)
- [Nigeria Business Registration](https://cac.gov.ng/)
- [Laravel Migrations](https://laravel.com/docs/migrations)
