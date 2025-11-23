<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class PermissionServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Blade::if('permission', function ($permission) {
            return auth()->check() && auth()->user()->hasPermission($permission);
        });

        Blade::if('role', function ($role) {
            return auth()->check() && auth()->user()->roles()->where('name', $role)->exists();
        });

        Blade::if('hasAnyPermission', function (...$permissions) {
            if (!auth()->check()) return false;
            foreach ($permissions as $permission) {
                if (auth()->user()->hasPermission($permission)) return true;
            }
            return false;
        });
    }
}
