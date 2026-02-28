<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Helpers\TenantHelper;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Mime\MimeTypeGuesserInterface;
use League\MimeTypeDetection\ExtensionMimeTypeDetector;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // When the php_fileinfo extension is not available (e.g. some servers),
        // register a fallback MIME type detector that uses file extensions.
        if (!extension_loaded('fileinfo')) {
            // Fallback for Flysystem / League MimeTypeDetector
            $this->app->bind(
                \League\MimeTypeDetection\MimeTypeDetector::class,
                ExtensionMimeTypeDetector::class
            );
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register fallback MIME guesser for Symfony when fileinfo is unavailable
        if (!extension_loaded('fileinfo')) {
            MimeTypes::getDefault()->addGuesser(
                new class implements MimeTypeGuesserInterface {
                    public function isGuesserSupported(): bool
                    {
                        return true;
                    }

                    public function guessMimeType(string $path): ?string
                    {
                        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                        if (!$ext) {
                            return 'application/octet-stream';
                        }
                        // Use Symfony's built-in extension→MIME map (no finfo needed)
                        $mimes = MimeTypes::getDefault()->getMimeTypes($ext);
                        return $mimes[0] ?? 'application/octet-stream';
                    }
                }
            );
        }

        // Share tenant with all views
        View::composer('*', function ($view) {
            $tenant = tenant(); // Use the tenant() helper function
            if ($tenant) {
                $view->with('tenantHelper', new TenantHelper());
                $view->with('tenant', $tenant);
            }
        });
    }
}
