# Audit Feature - Quick Reference Guide

## ✅ What's Been Implemented

### Database (9 migrations ran successfully)

-   ✅ customers: `created_by`, `updated_by`, `deleted_by`
-   ✅ vendors: `created_by`, `updated_by`, `deleted_by`
-   ✅ ledger_accounts: `created_by`, `updated_by`
-   ✅ vouchers: `updated_by` (already had created_by, posted_by)
-   ✅ sales: `created_by`, `updated_by`
-   ✅ product_categories: `created_by`, `updated_by`
-   ✅ cash_registers: `created_by`, `updated_by`
-   ✅ cash_register_sessions: `created_by`, `updated_by`
-   ✅ receipts: `created_by`, `updated_by`

### Models Updated

-   ✅ Customer, Vendor, Product, LedgerAccount, Sale, ProductCategory
-   ✅ Voucher, StockJournalEntry (with posting support)

### Traits Created

-   ✅ `HasAudit` - Auto-tracks created_by, updated_by, deleted_by
-   ✅ `HasPosting` - Provides post(), unpost(), cancel() methods

---

## 🚀 How It Works (Automatic)

### Creating Records

```php
$customer = Customer::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
]);
// created_by is automatically set to current user's ID
```

### Updating Records

```php
$customer->update(['email' => 'new@example.com']);
// updated_by is automatically set to current user's ID
```

### Deleting Records (Soft Delete)

```php
$customer->delete();
// deleted_by is automatically set to current user's ID
```

### Posting Vouchers

```php
$voucher->post();
// Sets: status='posted', posted_by=auth()->id(), posted_at=now()

$voucher->unpost();
// Sets: status='draft', posted_by=null, posted_at=null
```

---

## 📊 Accessing Audit Data

### Get User Who Created Record

```php
$creator = $customer->creator;
echo $creator->name; // "John Doe"
```

### Get User Who Last Updated Record

```php
$updater = $customer->updater;
echo $updater->name; // "Jane Smith"
```

### Get User Who Deleted Record

```php
$deleter = $customer->deleter;
echo $deleter?->name; // "Admin User"
```

### Get User Who Posted Record

```php
$poster = $voucher->poster;
echo $poster->name; // "Accountant"
```

---

## 🔍 Filtering Records

### By Creator

```php
$myCustomers = Customer::createdBy(auth()->id())->get();
```

### By Updater

```php
$updatedByUser = Customer::updatedBy($userId)->get();
```

### By Poster

```php
$postedByUser = Voucher::postedBy($userId)->get();
```

### By Status

```php
$draftVouchers = Voucher::draft()->get();
$postedVouchers = Voucher::posted()->get();
$cancelledVouchers = Voucher::cancelled()->get();
```

---

## ✔️ Checking Ownership

### Check if Current User Created

```php
if ($customer->wasCreatedByCurrentUser()) {
    // Allow editing
}
```

### Check if Current User Updated

```php
if ($customer->wasUpdatedByCurrentUser()) {
    // Show "Last modified by you"
}
```

### Check if Current User Posted

```php
if ($voucher->wasPostedByCurrentUser()) {
    // Allow unposting
}
```

---

## 🎯 Common Use Cases

### Display Creator in UI

```blade
<p>Created by: {{ $customer->creator->name ?? 'System' }}</p>
<p>Created at: {{ $customer->created_at->format('Y-m-d H:i:s') }}</p>
```

### Display Last Updater

```blade
@if($customer->updated_by)
    <p>Last updated by: {{ $customer->updater->name }}</p>
    <p>Updated at: {{ $customer->updated_at->format('Y-m-d H:i:s') }}</p>
@endif
```

### Show Posting Info

```blade
@if($voucher->posted_at)
    <span class="badge bg-success">
        Posted by {{ $voucher->poster->name }}
        on {{ $voucher->posted_at->format('Y-m-d') }}
    </span>
@endif
```

### Filter User's Own Records

```php
// In controller
$myCustomers = Customer::createdBy(auth()->id())
    ->orderBy('created_at', 'desc')
    ->paginate(20);
```

---

## 🔧 Adding Audit to New Models

### Step 1: Create Migration

```php
Schema::table('new_table', function (Blueprint $table) {
    $table->foreignId('created_by')->nullable()
        ->constrained('users')->onDelete('set null');
    $table->foreignId('updated_by')->nullable()
        ->constrained('users')->onDelete('set null');
});
```

### Step 2: Update Model

```php
use App\Traits\HasAudit;

class NewModel extends Model
{
    use HasAudit;

    protected $fillable = [
        // ... your fields
        'created_by',
        'updated_by',
    ];
}
```

### Step 3: Done!

Audit tracking now works automatically.

---

## 📱 For Postable Models

### Migration

```php
$table->enum('status', ['draft', 'posted', 'cancelled'])->default('draft');
$table->timestamp('posted_at')->nullable();
$table->foreignId('posted_by')->nullable()
    ->constrained('users')->onDelete('set null');
```

### Model

```php
use App\Traits\HasAudit;
use App\Traits\HasPosting;

class NewVoucher extends Model
{
    use HasAudit, HasPosting;

    protected $fillable = [
        // ... your fields
        'status',
        'posted_at',
        'posted_by',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'posted_at' => 'datetime',
    ];
}
```

### Usage

```php
// Post
$voucher->post(); // Auto-sets posted_by and posted_at

// Unpost
$voucher->unpost(); // Clears posted_by and posted_at

// Cancel
$voucher->cancel(); // Sets status to 'cancelled'

// Check status
if ($voucher->isPosted()) { }
if ($voucher->isDraft()) { }
if ($voucher->isCancelled()) { }
```

---

## 🎉 Benefits

✅ **Automatic** - No manual tracking needed
✅ **Consistent** - Works the same everywhere
✅ **Safe** - Nullable with foreign keys
✅ **Fast** - No performance impact
✅ **Easy** - Simple to query and display
✅ **Reliable** - Audit trail preserved even if user deleted

---

## 🤝 Questions?

Check the detailed implementation guide: `AUDIT_FEATURE_IMPLEMENTATION.md`
