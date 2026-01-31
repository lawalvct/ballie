<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array|null get()
 * @method static array getDefault()
 * @method static string key()
 * @method static string name()
 * @method static string company()
 * @method static string logo()
 * @method static string favicon()
 * @method static string primaryColor()
 * @method static string secondaryColor()
 * @method static bool is(string $brandKey)
 * @method static string tagline()
 * @method static string supportEmail()
 * @method static string|null whatsapp()
 * @method static array toArray()
 *
 * @see \App\Services\BrandService
 */
class Brand extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Services\BrandService::class;
    }
}
