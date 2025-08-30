<?php

namespace App\Http\Controllers\Tenant\Documents;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;

class DocumentsController extends Controller
{
    /**
     * Display the documents dashboard
     */
    public function index(Request $request, Tenant $tenant)
    {
        $currentTenant = $tenant;
        $user = auth()->user();

        // You would typically load documents data here
        // For example:
        // $totalDocuments = Document::where('tenant_id', $tenant->id)->count();
        // $recentDocuments = Document::where('tenant_id', $tenant->id)->latest()->take(10)->get();
        // $documentsByType = Document::where('tenant_id', $tenant->id)->groupBy('type')->selectRaw('type, count(*) as count')->get();

        return view('tenant.documents.index', [
            'currentTenant' => $currentTenant,
            'user' => $user,
            'tenant' => $currentTenant,
        ]);
    }
}
