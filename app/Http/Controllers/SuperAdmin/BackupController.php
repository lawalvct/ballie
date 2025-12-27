<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\CyberPanelEmailService;
use Illuminate\Http\Request;

class BackupController extends Controller
{
    protected $cyberPanelService;

    public function __construct(CyberPanelEmailService $cyberPanelService)
    {
        $this->cyberPanelService = $cyberPanelService;
    }

    /**
     * Display backup management page
     */
    public function index()
    {
        return view('super-admin.backups.index');
    }

    /**
     * Create a server backup
     */
    public function createServerBackup(Request $request)
    {
        $website = 'ballie.co';

        $result = $this->cyberPanelService->createBackup($website);

        if ($result['success']) {
            return back()->with('success', $result['message'] ?? 'Server backup created successfully');
        }

        return back()->with('error', $result['error'] ?? 'Failed to create server backup');
    }

    /**
     * Create a local backup
     */
    public function createLocalBackup(Request $request)
    {
        // TODO: Implement local backup functionality
        return back()->with('info', 'Local backup feature coming soon');
    }
}
