<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function download($export)
    {
        // TODO: Implement export download functionality
        return response()->json(['message' => 'Export download endpoint - not implemented yet']);
    }

    public function status($export)
    {
        // TODO: Implement export status functionality
        return response()->json(['message' => 'Export status endpoint - not implemented yet']);
    }
}
