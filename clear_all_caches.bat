@echo off
echo ========================================
echo Clearing ALL Laravel Caches
echo ========================================
echo.

echo [1/8] Clearing route cache...
php artisan route:clear

echo [2/8] Clearing config cache...
php artisan config:clear

echo [3/8] Clearing application cache...
php artisan cache:clear

echo [4/8] Clearing view cache...
php artisan view:clear

echo [5/8] Clearing compiled files...
php artisan clear-compiled

echo [6/8] Clearing event cache...
php artisan event:clear

echo [7/8] Running optimize clear...
php artisan optimize:clear

echo [8/8] Generating fresh optimized files...
php artisan optimize

echo.
echo ========================================
echo All caches cleared successfully!
echo ========================================
echo.
echo Now listing admin user routes to verify:
echo.
php artisan route:list --name=tenant.admin.users --columns=method,uri,name

echo.
echo ========================================
echo DONE! Please restart your dev server.
echo ========================================
pause
