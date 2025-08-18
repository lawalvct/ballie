<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    /**
     * Display the community forum
     */
    public function index()
    {
        return view('tenant.community.index');
    }
}