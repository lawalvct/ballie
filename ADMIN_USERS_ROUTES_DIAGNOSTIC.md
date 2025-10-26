# Admin Users Routes Diagnostic & Fix Guide

## Summary of Investigation

### ✅ What's Correct

1. **Route Definitions**: All routes in `routes/tenant.php` correctly use `{userId}` parameter
2. **Controller Methods**: All AdminController methods correctly expect `$userId` parameter
3. **Views**: All Blade views correctly pass `$user->id` to route helpers
4. **No Duplicates**: No duplicate route registrations found in active route files

### ❌ What Was Wrong

1. **Stale Route Cache**: Laravel was serving cached routes from previous configuration
2. **Stale View Cache**: Compiled Blade views contained old route references
3. **Old Documentation**: `old_tenant_routes.md` and `ADMIN_MANAGEMENT_FLOW.md` show outdated `{user}` parameter (documentation only, not affecting runtime)

---

## Step-by-Step Fix Instructions

### Option 1: Quick Fix (Run the Batch Script)

Simply run the cache clearing script I created:

```powershell
cd C:\laragon\www\ballie
.\clear_all_caches.bat
```

This will:

-   Clear all Laravel caches (route, config, view, application, compiled, event)
-   Run optimize:clear and optimize
-   Display the current admin user routes for verification
-   Prompt you to restart your dev server

### Option 2: Manual Fix (If you prefer PowerShell)

```powershell
# Navigate to project root
cd C:\laragon\www\ballie

# Clear all caches
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan clear-compiled
php artisan event:clear
php artisan optimize:clear
php artisan optimize

# Verify routes are correct
php artisan route:list --name=tenant.admin.users --columns=method,uri,name

# Restart your dev server (if using artisan serve)
# Stop current server (Ctrl+C), then:
php artisan serve
```

---

## Verification Steps

### 1. Check Route Registration (Browser-based Diagnostic)

I've added temporary diagnostic endpoints. After clearing caches, visit:

```
http://localhost:8000/{your-tenant-slug}/admin/diagnostic/routes
```

This will return JSON showing:

-   Which routes are registered
-   Their URIs and parameters
-   Tenant context
-   User query results
-   Generated test URLs

### 2. Test Specific User Access

```
http://localhost:8000/{your-tenant-slug}/admin/diagnostic/user/71
```

Replace `71` with any user ID. This will show if the user can be found in tenant scope.

### 3. Manual Browser Test

After clearing caches, try accessing:

```
http://localhost:8000/profund-solution-ltd/admin/users/71
```

Should now display the user show page without 404.

### 4. Command Line Verification

```powershell
# List all admin.users routes with details
php artisan route:list --name=tenant.admin.users

# Should show:
# GET    {tenant}/admin/users                tenant.admin.users.index
# GET    {tenant}/admin/users/create         tenant.admin.users.create
# POST   {tenant}/admin/users                tenant.admin.users.store
# GET    {tenant}/admin/users/{userId}/edit  tenant.admin.users.edit
# GET    {tenant}/admin/users/{userId}       tenant.admin.users.show
# PUT    {tenant}/admin/users/{userId}       tenant.admin.users.update
# DELETE {tenant}/admin/users/{userId}       tenant.admin.users.destroy
```

---

## Expected Results

### Before Fix

-   ❌ `route:list` might show `{user}` instead of `{userId}`
-   ❌ `http://localhost:8000/profund-solution-ltd/admin/users/71` → 404
-   ❌ Views throw "Route [tenant.admin.users.show] not defined"

### After Fix

-   ✅ `route:list` shows `{userId}` for all dynamic routes
-   ✅ `http://localhost:8000/profund-solution-ltd/admin/users/71` → Shows user details
-   ✅ All view route helpers generate correct URLs
-   ✅ Edit/Show/Delete actions work correctly

---

## Troubleshooting

### If routes still show 404 after clearing caches:

1. **Check if user exists in tenant scope:**

    ```php
    php artisan tinker
    >>> use App\Models\User;
    >>> User::where('tenant_id', 'your-tenant-id')->where('id', 71)->first()
    ```

2. **Verify middleware is not blocking:**

    - Check that you're logged in
    - Check that email is verified
    - Check that onboarding is completed
    - Check that subscription is active

3. **Check web server rewrites:**

    - If using Laragon/Apache, ensure `.htaccess` is present
    - If using nginx, check rewrite rules

4. **Clear browser cache:**
    - Hard refresh: Ctrl+Shift+R (Chrome/Firefox)
    - Or open in incognito/private mode

### If diagnostic routes return errors:

Check `storage/logs/laravel.log` for detailed error messages:

```powershell
tail -n 50 storage\logs\laravel.log
# Or use PowerShell:
Get-Content storage\logs\laravel.log -Tail 50
```

---

## Clean-up After Verification

Once everything is working, remove the diagnostic code:

### 1. Remove diagnostic routes from `routes/tenant.php`:

Delete these lines (around line 577):

```php
// Diagnostic routes (temporary - remove in production)
Route::prefix('diagnostic')->name('diagnostic.')->group(function () {
    Route::get('/routes', [\App\Http\Controllers\Tenant\Admin\DiagnosticController::class, 'checkRoutes'])->name('routes');
    Route::get('/user/{userId}', [\App\Http\Controllers\Tenant\Admin\DiagnosticController::class, 'testUserAccess'])->name('user');
});
```

### 2. Delete diagnostic controller:

```powershell
del app\Http\Controllers\Tenant\Admin\DiagnosticController.php
```

### 3. Keep the cache-clearing batch script for future use:

The `clear_all_caches.bat` script is useful for development, so you can keep it.

---

## Root Cause Analysis

### Why did this happen?

1. **Route Evolution**: The codebase was refactored from using `{user}` (with route model binding) to `{userId}` (manual tenant-scoped queries)

2. **Cache Persistence**: Laravel caches compiled routes and views for performance. When route definitions changed, the cache wasn't cleared

3. **Multi-tenant Complexity**: Route model binding doesn't automatically respect tenant scope, requiring the manual scoping approach

### Why the manual approach is better:

```php
// Old way (route model binding - doesn't scope to tenant automatically)
Route::get('/{user}', function (User $user) { ... });

// New way (manual tenant-scoped query - safer in multi-tenant)
Route::get('/{userId}', function ($userId) {
    $user = User::where('tenant_id', tenant()->id)->findOrFail($userId);
    // This prevents cross-tenant data leaks
});
```

---

## Files Modified/Created

### Created:

1. `clear_all_caches.bat` - Comprehensive cache clearing script
2. `app/Http/Controllers/Tenant/Admin/DiagnosticController.php` - Diagnostic endpoints
3. `ADMIN_USERS_ROUTES_DIAGNOSTIC.md` - This documentation

### Modified:

1. `routes/tenant.php` - Added temporary diagnostic routes

### Already Correct (No changes needed):

1. `routes/tenant.php` - User routes section
2. `app/Http/Controllers/Tenant/Admin/AdminController.php` - All user methods
3. `resources/views/tenant/admin/users/*.blade.php` - All user views

---

## Quick Reference Commands

```powershell
# Clear all caches (recommended)
.\clear_all_caches.bat

# OR manually:
php artisan optimize:clear

# Check routes
php artisan route:list --name=tenant.admin.users

# Check for errors
Get-Content storage\logs\laravel.log -Tail 50

# Restart server
# Ctrl+C to stop, then:
php artisan serve
```

---

## Support

If issues persist after following these steps, check:

1. Laravel log: `storage/logs/laravel.log`
2. Web server error log (Apache/Nginx)
3. Browser console for JavaScript errors
4. Network tab for actual URLs being requested

The diagnostic endpoints will help identify:

-   Whether routes are registered correctly
-   Whether tenant context is correct
-   Whether users can be queried in tenant scope
-   What URLs are being generated by route helpers
