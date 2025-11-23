<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;

class HelpController extends Controller
{
    public function index(Tenant $tenant)
    {
        return view('tenant.help.index', compact('tenant'));
    }
}
