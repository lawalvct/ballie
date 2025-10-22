# Laravel Debugbar + VSCode Setup Guide

## ✅ Installation Complete

Laravel Debugbar has been successfully installed and configured to work with VSCode and Telescope!

---

## What Was Installed

### 1. Laravel Debugbar Package

```bash
composer require barryvdh/laravel-debugbar --dev
```

**Version Installed:** `^3.16`

**Package Location:** `vendor/barryvdh/laravel-debugbar`

---

## Configuration Applied

### 1. Environment Variables (.env)

Added the following configurations:

```env
# Debugbar Configuration
DEBUGBAR_ENABLED=true
DEBUGBAR_EDITOR=vscode
DEBUGBAR_LOCAL_SITES_PATH=c:\laragon\www\ballie

# Editor Configuration for Error Pages
IGNITION_EDITOR=vscode
IGNITION_LOCAL_SITES_PATH=c:\laragon\www\ballie
```

**What these do:**

-   `DEBUGBAR_ENABLED=true` - Enables Debugbar in local environment
-   `DEBUGBAR_EDITOR=vscode` - Sets VSCode as default editor for file links
-   `DEBUGBAR_LOCAL_SITES_PATH` - Maps server paths to local paths
-   `IGNITION_EDITOR=vscode` - Sets VSCode for Laravel error pages too

### 2. Published Configuration

Created: `config/debugbar.php`

**Key Settings:**

```php
'enabled' => env('DEBUGBAR_ENABLED', null),
'editor' => env('DEBUGBAR_EDITOR') ?: env('IGNITION_EDITOR', 'phpstorm'),
'local_sites_path' => env('DEBUGBAR_LOCAL_SITES_PATH', env('IGNITION_LOCAL_SITES_PATH')),
'except' => [
    'telescope*',  // Debugbar won't interfere with Telescope
    'horizon*',
],
```

---

## How It Works

### Debugbar Features

When you visit any page in your application, you'll see a debug bar at the bottom with:

1. **Messages** - Debug messages and dumps
2. **Timeline** - Request timeline with events
3. **Exceptions** - Any exceptions thrown
4. **Views** - Views rendered and their data
5. **Route** - Current route information
6. **Queries** - All database queries with execution time
7. **Models** - Eloquent models loaded
8. **Mail** - Emails sent (not actually sent in dev)
9. **Gate** - Authorization checks
10. **Session** - Session data
11. **Request** - Request data (POST, GET, headers, etc.)

### VSCode Integration

**Click to Open in VSCode:**

When you see file paths in:

-   Debugbar tabs
-   Error pages (Ignition/Telescope)
-   Stack traces

Clicking the file name will open it directly in VSCode at the exact line number!

**Example:**

```
App\Http\Controllers\DashboardController.php:45
                                            ↑
                                   Click opens VSCode at line 45
```

---

## Using Debugbar

### 1. Basic Debug Messages

```php
// In your controller or anywhere
Debugbar::info('User logged in');
Debugbar::warning('This might be a problem');
Debugbar::error('Something went wrong');

// Add variables
Debugbar::info(['user_id' => $user->id, 'name' => $user->name]);
```

### 2. Timing Operations

```php
Debugbar::startMeasure('render', 'Rendering dashboard');
// ... your code ...
Debugbar::stopMeasure('render');

// Shorter syntax
Debugbar::addMeasure('Query Time', 0.1234);
```

### 3. Custom Messages

```php
// Simple message
debugbar()->info('Processing payment');

// With context
debugbar()->info('Payment processed', [
    'amount' => $amount,
    'customer' => $customer->name
]);
```

### 4. Alternative: Using dump()

```php
// Traditional dd() but won't stop execution
dump($variable);

// Still works to stop execution
dd($variable);
```

### 5. Measure Database Queries

Debugbar automatically tracks all queries:

-   Shows SQL with parameters
-   Execution time
-   Duplicate query detection
-   Slow query highlighting

---

## Working with Telescope

### No Conflicts!

Debugbar and Telescope work together perfectly:

**Debugbar:** Quick debugging during development

-   Shows on every page
-   Instant feedback
-   No navigation needed

**Telescope:** Deep inspection and monitoring

-   Access at: `http://localhost:8000/telescope`
-   Historical data
-   Advanced filtering
-   Request/response inspection

### Excluded Routes

Debugbar won't show on Telescope pages (configured in `config/debugbar.php`):

```php
'except' => [
    'telescope*',
    'horizon*',
],
```

---

## VSCode Setup

### 1. Install VSCode URL Handler (if not installed)

The integration should work automatically, but if clicking files doesn't open VSCode:

**Windows:**

1. Open VSCode
2. Press `Ctrl+Shift+P`
3. Type: `Shell Command: Install 'code' command in PATH`
4. Click it to install

### 2. Test the Integration

Create a test route:

```php
// routes/web.php
Route::get('/test-debugbar', function() {
    Debugbar::info('Testing VSCode integration');
    Debugbar::warning('Click this file link: ' . __FILE__);

    return view('welcome');
});
```

Visit: `http://localhost:8000/test-debugbar`

Click the file path in Debugbar → Should open in VSCode!

---

## Performance Considerations

### Debugbar is Development Only

```php
// config/debugbar.php
'enabled' => env('DEBUGBAR_ENABLED', null),
```

When `APP_DEBUG=false` (production), Debugbar is automatically disabled.

### Disable for API Routes (Optional)

If you want to disable Debugbar for API routes:

```php
// config/debugbar.php
'except' => [
    'telescope*',
    'horizon*',
    'api/*',  // Add this
],
```

### Ajax Requests

Debugbar handles AJAX automatically:

-   Stores data for AJAX requests
-   View in Debugbar's "History" section
-   Click any request to see details

---

## Customization

### Change Collectors

Edit `config/debugbar.php` to enable/disable collectors:

```php
'collectors' => [
    'phpinfo'         => true,  // Php version
    'messages'        => true,  // Messages
    'time'            => true,  // Time Datalogger
    'memory'          => true,  // Memory usage
    'exceptions'      => true,  // Exception displayer
    'log'             => true,  // Logs from Monolog
    'db'              => true,  // Show database queries
    'views'           => true,  // Views with their data
    'route'           => true,  // Current route information
    'auth'            => true,  // Display Laravel authentication status
    'gate'            => true,  // Display Laravel Gate checks
    'session'         => true,  // Display session data
    'symfony_request' => true,  // Only one can be enabled..
    'mail'            => true,  // Catch mail messages
    'laravel'         => false, // Laravel version and environment
    'events'          => false, // All events fired
    'default_request' => false, // Regular or special Symfony request logger
    'logs'            => false, // Add the latest log messages
    'files'           => false, // Show the included files
    'config'          => false, // Display config settings
    'cache'           => false, // Display cache events
    'models'          => true,  // Display models
    'livewire'        => true,  // Display Livewire components
],
```

### Query Performance Thresholds

Highlight slow queries:

```env
# .env
DEBUGBAR_OPTIONS_DB_SLOW_THRESHOLD=100  # Highlight queries > 100ms
```

### Database Query Limits

Prevent overwhelming data on pages with many queries:

```env
DEBUGBAR_OPTIONS_DB_SOFT_LIMIT=100   # After 100 queries, no params/backtrace
DEBUGBAR_OPTIONS_DB_HARD_LIMIT=500   # After 500 queries, ignore
```

---

## Keyboard Shortcuts

When Debugbar is open:

-   **`ESC`** - Close Debugbar
-   **Click tab** - Switch between collectors
-   **Right-click query** - Copy SQL
-   **Click file path** - Open in VSCode

---

## Troubleshooting

### VSCode Not Opening Files

**Problem:** Clicking files doesn't open VSCode

**Solution 1:** Check VSCode is in PATH

```bash
code --version
```

If error, install 'code' command:

-   Open VSCode
-   `Ctrl+Shift+P` → "Shell Command: Install 'code' command in PATH"

**Solution 2:** Check path mapping

```env
DEBUGBAR_LOCAL_SITES_PATH=c:\laragon\www\ballie  # Must match exactly
```

**Solution 3:** Use vscode-insiders if you use Insiders version

```env
DEBUGBAR_EDITOR=vscode-insiders
```

### Debugbar Not Showing

**Problem:** No debug bar visible

**Check 1:** Is debug enabled?

```env
APP_DEBUG=true
DEBUGBAR_ENABLED=true
```

**Check 2:** Clear config cache

```bash
php artisan config:clear
php artisan cache:clear
```

**Check 3:** Check if it's an excluded route

```php
// config/debugbar.php
'except' => [
    'telescope*',
    'api/*',  // Check your route isn't here
],
```

### Wrong Path in VSCode

**Problem:** Opens file but wrong location

**Fix path mapping:**

```env
# If using Docker/Homestead:
DEBUGBAR_REMOTE_SITES_PATH=/var/www/html
DEBUGBAR_LOCAL_SITES_PATH=c:\laragon\www\ballie

# If local:
DEBUGBAR_REMOTE_SITES_PATH=
DEBUGBAR_LOCAL_SITES_PATH=c:\laragon\www\ballie
```

### Conflicts with Telescope

**Problem:** Debugbar interfering with Telescope

**Already configured!** Check:

```php
// config/debugbar.php
'except' => [
    'telescope*',  // ✅ Already excludes Telescope
],
```

---

## Advanced Usage

### Measure Custom Code

```php
use Debugbar\Debugbar;

class ReportGenerator
{
    public function generate()
    {
        Debugbar::startMeasure('report_generation', 'Generating Financial Report');

        // Complex calculations
        $data = $this->processData();

        Debugbar::addMeasure('Data Processing',
            Debugbar::getMeasure('report_generation')['duration']
        );

        Debugbar::stopMeasure('report_generation');

        return $data;
    }
}
```

### Custom Tab with Collections

```php
use DebugBar\DataCollector\DataCollector;

Debugbar::addCollector(new MyCustomCollector());
```

### Disable on Specific Routes

```php
// In a controller
public function index()
{
    if (app()->environment('local')) {
        \Debugbar::disable();
    }

    return view('sensitive-page');
}
```

---

## Integration with Your ERP

### Dashboard Queries

Now you can see all dashboard queries:

-   Revenue calculations
-   Expense aggregations
-   Customer counts
-   Product summaries

**Check query performance:**

1. Visit dashboard
2. Open Debugbar → Queries tab
3. Look for slow queries (highlighted)
4. Optimize as needed

### Ledger Account Debugging

Debug balance calculations:

```php
// In LedgerAccountController
public function show(LedgerAccount $account)
{
    Debugbar::info('Calculating balance for: ' . $account->name);

    $balance = $account->getCurrentBalance();

    Debugbar::info('Balance calculated', [
        'account' => $account->name,
        'balance' => $balance,
        'type' => $account->account_type
    ]);

    return view('...', compact('account', 'balance'));
}
```

### Track Tenant Switching

```php
// See which tenant context you're in
Debugbar::info('Current Tenant', [
    'id' => tenant()->id,
    'name' => tenant()->name,
    'database' => tenant()->database
]);
```

---

## Best Practices

### 1. Use Descriptive Messages

**❌ Bad:**

```php
Debugbar::info($data);
```

**✅ Good:**

```php
Debugbar::info('User Registration Data', [
    'email' => $data['email'],
    'tenant' => $data['tenant_id']
]);
```

### 2. Measure Performance-Critical Code

```php
Debugbar::startMeasure('ledger_calculation', 'Calculating Ledger Balances');
// ... complex calculation ...
Debugbar::stopMeasure('ledger_calculation');
```

### 3. Use Different Log Levels

```php
Debugbar::info('Normal information');
Debugbar::warning('Potential issue');
Debugbar::error('Something failed');
```

### 4. Clean Up Debug Code

Use helpers that are safe in production:

```php
// This won't error in production when Debugbar is disabled
app('debugbar')->info('Safe debug message');

// Or check if enabled
if (config('debugbar.enabled')) {
    Debugbar::info('Debug info');
}
```

---

## Quick Reference

### Installation

```bash
composer require barryvdh/laravel-debugbar --dev
php artisan vendor:publish --provider="Barryvdh\Debugbar\ServiceProvider"
```

### Configuration

```env
DEBUGBAR_ENABLED=true
DEBUGBAR_EDITOR=vscode
DEBUGBAR_LOCAL_SITES_PATH=c:\laragon\www\ballie
IGNITION_EDITOR=vscode
```

### Common Methods

```php
Debugbar::info($data);
Debugbar::warning($message);
Debugbar::error($exception);
Debugbar::startMeasure($name, $label);
Debugbar::stopMeasure($name);
Debugbar::addMeasure($label, $start, $end);
```

### Disable/Enable

```php
Debugbar::disable();
Debugbar::enable();
```

---

## Resources

-   **Debugbar Documentation:** https://github.com/barryvdh/laravel-debugbar
-   **VSCode Integration:** https://github.com/barryvdh/laravel-debugbar#editor-configuration
-   **Telescope Docs:** https://laravel.com/docs/10.x/telescope

---

## Summary

✅ **Installed:** Laravel Debugbar v3.16
✅ **Configured:** VSCode as default editor
✅ **Integrated:** Works alongside Telescope
✅ **Ready:** Click any file link to open in VSCode

**Test it now:**

1. Visit any page in your application
2. See the debug bar at the bottom
3. Click on file paths → Opens in VSCode!
4. Check the Queries tab → See all database queries

**Happy Debugging! 🐛🔍**

---

**Updated:** <?= date('Y-m-d H:i:s') ?>
**Project:** Ballie ERP
**Environment:** Local Development
