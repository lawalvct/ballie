<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    /**
     * Display the support page
     */
    public function index()
    {
        return view('tenant.support.index');
    }

    /**
     * Store a new support request
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:low,medium,high',
        ]);

        // Process support request
        // You would typically send an email or create a ticket in your support system

        return back()->with('success', 'Your support request has been submitted. We\'ll get back to you shortly.');
    }
}