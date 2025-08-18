<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HelpController extends Controller
{
    /**
     * Display video tutorials
     */
    public function videos()
    {
        return view('tenant.help.videos');
    }

    /**
     * Display help articles
     */
    public function articles()
    {
        return view('tenant.help.articles');
    }
}