<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\CyberPanelEmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EmailController extends Controller
{
    protected $emailService;

    public function __construct(CyberPanelEmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Display a listing of email accounts
     */
    public function index(Request $request)
    {
        // Fixed domain - ballie.co
        $domain = 'ballie.co';
        $emails = [];

        // Get emails for ballie.co domain
        $result = $this->emailService->listEmails($domain);
        if ($result['success']) {
            $data = $result['data']['data'] ?? [];
            // Parse JSON string if it's a string
            if (is_string($data)) {
                $emails = json_decode($data, true) ?? [];
            } else {
                $emails = is_array($data) ? $data : [];
            }
        }

        return view('super-admin.emails.index', compact('emails', 'domain'));
    }

    /**
     * Show the form for creating a new email account
     */
    public function create()
    {
        // Fixed domain - ballie.co
        return view('super-admin.emails.create');
    }

    /**
     * Store a newly created email account
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'domain' => 'required|string',
            'username' => 'required|string|min:3|max:50|regex:/^[a-zA-Z0-9._-]+$/',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'username.regex' => 'Username can only contain letters, numbers, dots, underscores, and hyphens',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $result = $this->emailService->createEmail(
            $request->domain,
            $request->username,
            $request->password
        );

        if ($result['success']) {
            return redirect()
                ->route('super-admin.emails.index', ['domain' => $request->domain])
                ->with('success', 'Email account created successfully: ' . $request->username . '@' . $request->domain);
        }

        return back()
            ->withInput()
            ->with('error', $result['error'] ?? 'Failed to create email account');
    }

    /**
     * Delete an email account
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'domain' => 'required|string',
            'username' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'Invalid request parameters');
        }

        // Construct full email address
        $email = $request->username . '@' . $request->domain;

        $result = $this->emailService->deleteEmail($email);

        if ($result['success']) {
            return back()->with('success', 'Email account deleted successfully');
        }

        return back()->with('error', $result['error'] ?? 'Failed to delete email account');
    }

    /**
     * Show form to change email password
     */
    public function editPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'domain' => 'required|string',
            'username' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'Invalid request parameters');
        }

        return view('super-admin.emails.change-password', [
            'domain' => $request->domain,
            'username' => $request->username,
        ]);
    }

    /**
     * Update email password
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'domain' => 'required|string',
            'username' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $result = $this->emailService->changeEmailPassword(
            $request->domain,
            $request->username,
            $request->password
        );

        if ($result['success']) {
            return redirect()
                ->route('super-admin.emails.index', ['domain' => $request->domain])
                ->with('success', 'Email password changed successfully');
        }

        return back()
            ->withInput()
            ->with('error', $result['error'] ?? 'Failed to change email password');
    }

    /**
     * Generate a random strong password
     */
    public function generatePassword()
    {
        $password = Str::random(16);
        return response()->json(['password' => $password]);
    }

    /**
     * Test CyberPanel API connection and token
     */
    public function testConnection()
    {
        $testResults = [
            'config' => [
                'api_url' => config('services.cyberpanel.api_url'),
                'username' => config('services.cyberpanel.username'),
                'password_set' => !empty(config('services.cyberpanel.password')),
            ],
            'token' => null,
            'domains_test' => null,
        ];

        // Test token generation
        try {
            $credentials = config('services.cyberpanel.username') . ':' . config('services.cyberpanel.password');
            $token = "Basic " . base64_encode($credentials);
            $testResults['token'] = $token;
            $testResults['token_decoded'] = base64_decode(str_replace('Basic ', '', $token));
        } catch (\Exception $e) {
            $testResults['token_error'] = $e->getMessage();
        }

        // Test API call to fetch domains
        try {
            $result = $this->emailService->listDomains();
            $testResults['domains_test'] = $result;
        } catch (\Exception $e) {
            $testResults['domains_test_error'] = $e->getMessage();
        }

        return response()->json($testResults, 200, [], JSON_PRETTY_PRINT);
    }
}
