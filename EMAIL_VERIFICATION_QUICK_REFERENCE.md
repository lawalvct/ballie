# Email Verification Quick Reference

## Key Routes

-   **Verification Form**: `GET /verify-email` → `route('verification.notice')`
-   **Submit Code**: `POST /verify-email` → `route('verification.verify')`
-   **Resend Code**: `POST /verification-code/resend` → `route('verification.resend')`

## Key Methods

### Check if User is Verified

```php
$user->hasVerifiedEmail(); // Returns boolean
```

### Mark User as Verified

```php
$user->markEmailAsVerified();
// or
$user->email_verified_at = now();
$user->save();
```

### Check if Tenant Owner is Verified

```php
$tenant->is_verified; // Returns boolean
```

### Generate Verification Code

```php
$code = sprintf('%04d', random_int(0, 9999));
```

### Store Verification Code

```php
DB::table('email_verification_codes')->insert([
    'user_id' => $user->id,
    'code' => $code,
    'expires_at' => now()->addMinutes(60),
    'created_at' => now(),
    'updated_at' => now(),
]);
```

### Send Verification Email

```php
use App\Notifications\WelcomeNotification;

$user->notify(new WelcomeNotification($code));
```

## Blade Directives

### Check if Email Verified in Views

```blade
@if(Auth::user()->hasVerifiedEmail())
    <!-- Show verified content -->
@else
    <!-- Show unverified notice -->
@endif
```

### Show Verification Link

```blade
<a href="{{ route('verification.notice') }}">
    Verify Your Email
</a>
```

## Middleware Usage

### Protect Single Route

```php
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('email.verified');
```

### Protect Route Group

```php
Route::middleware(['auth', 'email.verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/profile', [ProfileController::class, 'index']);
});
```

## Configuration

### Code Expiration (Minutes)

Currently: 60 minutes
Location: `app/Http/Controllers/Auth/RegisteredUserController.php` and `EmailVerificationController.php`

```php
'expires_at' => now()->addMinutes(60)
```

### Rate Limiting

Currently: 6 attempts per minute
Location: `routes/auth.php`

```php
->middleware('throttle:6,1')
```

## Database Queries

### Get Verification Code

```php
$code = DB::table('email_verification_codes')
    ->where('user_id', $userId)
    ->where('expires_at', '>', now())
    ->first();
```

### Delete User's Codes

```php
DB::table('email_verification_codes')
    ->where('user_id', $userId)
    ->delete();
```

### Delete Expired Codes (Cleanup)

```php
DB::table('email_verification_codes')
    ->where('expires_at', '<', now())
    ->delete();
```

## Common Errors & Solutions

### Error: "Invalid or expired verification code"

**Causes**:

-   Code has expired (60 minutes passed)
-   Code was already used
-   Wrong code entered
-   Code doesn't match user

**Solution**:

-   Click "Resend Verification Code"
-   Check email for new code

### Error: Route not found

**Cause**: Cache not cleared

**Solution**:

```bash
php artisan optimize:clear
```

### Error: Middleware not working

**Causes**:

-   Middleware not registered
-   Cache not cleared

**Solution**:

1. Check `app/Http/Kernel.php` has middleware alias
2. Run `php artisan optimize:clear`

### Email not sending

**Causes**:

-   Queue not running
-   Mail configuration incorrect
-   SMTP credentials wrong

**Solutions**:

1. Start queue: `php artisan queue:work`
2. Check `.env` mail settings
3. Test mail: `php artisan tinker` → `Mail::raw('Test', function($m) { $m->to('test@example.com')->subject('Test'); });`

## Testing Commands

### Create Test User

```php
php artisan tinker

$user = User::factory()->create([
    'email' => 'test@example.com',
    'email_verified_at' => null
]);
```

### Generate Test Code

```php
php artisan tinker

$code = sprintf('%04d', random_int(0, 9999));
DB::table('email_verification_codes')->insert([
    'user_id' => 1,
    'code' => $code,
    'expires_at' => now()->addMinutes(60),
    'created_at' => now(),
    'updated_at' => now(),
]);
echo $code;
```

### Check Verification Status

```php
php artisan tinker

$user = User::find(1);
echo $user->hasVerifiedEmail() ? 'Verified' : 'Not Verified';
```

### Mark User as Verified (Manual)

```php
php artisan tinker

$user = User::find(1);
$user->markEmailAsVerified();
```

## Development Tips

### Disable Verification (Development Only)

Temporarily remove middleware from routes:

```php
// In routes/tenant.php
Route::middleware(['onboarding.completed', 'subscription.check'])->group(function () {
    // Remove 'email.verified' middleware
});
```

### View All Verification Codes

```php
php artisan tinker

DB::table('email_verification_codes')->get();
```

### Count Expired Codes

```php
php artisan tinker

DB::table('email_verification_codes')
    ->where('expires_at', '<', now())
    ->count();
```

## API Integration (Future)

If you need API verification:

### Generate Code (API)

```php
Route::post('/api/verification/send', function(Request $request) {
    $user = $request->user();

    // Generate code
    $code = sprintf('%04d', random_int(0, 9999));

    // Store code
    DB::table('email_verification_codes')->insert([
        'user_id' => $user->id,
        'code' => $code,
        'expires_at' => now()->addMinutes(60),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Send email
    $user->notify(new WelcomeNotification($code));

    return response()->json(['message' => 'Code sent']);
});
```

### Verify Code (API)

```php
Route::post('/api/verification/verify', function(Request $request) {
    $request->validate(['code' => 'required|digits:4']);

    $user = $request->user();

    $verification = DB::table('email_verification_codes')
        ->where('user_id', $user->id)
        ->where('code', $request->code)
        ->where('expires_at', '>', now())
        ->first();

    if (!$verification) {
        return response()->json(['error' => 'Invalid code'], 400);
    }

    $user->markEmailAsVerified();

    DB::table('email_verification_codes')
        ->where('id', $verification->id)
        ->delete();

    return response()->json(['message' => 'Email verified']);
});
```

## Email Template Customization

### Change Email Subject

```php
// In app/Notifications/WelcomeNotification.php
->subject('Your Custom Subject Here')
```

### Add Logo to Email

```php
->line('<img src="' . asset('images/logo.png') . '" alt="Logo">')
```

### Change Button Color

```php
->action('Verify Email Now', route('verification.notice'))
```

### Add Footer

```php
->salutation('Best regards, The ' . config('app.name') . ' Team')
```

## Support & Maintenance

### Monitor Verification Success Rate

```php
// Add to admin dashboard
$totalRegistrations = User::whereDate('created_at', today())->count();
$verifiedToday = User::whereDate('email_verified_at', today())->count();
$successRate = $totalRegistrations > 0 ? ($verifiedToday / $totalRegistrations) * 100 : 0;
```

### Alert on Low Success Rate

```php
if ($successRate < 50) {
    // Send notification to admin
    \Log::warning('Low email verification rate', [
        'rate' => $successRate,
        'total' => $totalRegistrations,
        'verified' => $verifiedToday
    ]);
}
```
