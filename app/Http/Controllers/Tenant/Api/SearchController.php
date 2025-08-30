<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        // Placeholder search functionality
        return response()->json([
            'results' => [],
            'query' => $request->get('q', ''),
            'total' => 0
        ]);
    }
}
