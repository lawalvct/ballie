<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DetectBrand
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->attributes->has('brand')) {
            return $next($request);
        }

        $brand = $this->resolveBrand($request);

        $request->attributes->set('brand', $brand);
        app()->instance('brand', $brand);
        config(['app.brand' => $brand['key'] ?? null]);

        view()->share('brand', $brand);

        return $next($request);
    }

    protected function resolveBrand(Request $request): array
    {
        $brands = config('brands.brands', []);
        $defaultKey = config('brands.default', 'ballie');

        $envBrand = env('BRAND');
        if ($envBrand && isset($brands[$envBrand])) {
            return $brands[$envBrand];
        }

        $host = $request->getHost();
        foreach ($brands as $brand) {
            if (!empty($brand['domains']) && in_array($host, $brand['domains'], true)) {
                return $brand;
            }
        }

        return $brands[$defaultKey] ?? reset($brands) ?: [
            'key' => $defaultKey,
            'name' => ucfirst($defaultKey),
            'company' => ucfirst($defaultKey),
            'landing_view' => 'welcome',
        ];
    }
}
