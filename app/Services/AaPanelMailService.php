<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AaPanelMailService
{
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.aapanel.url', 'https://127.0.0.1:31947'), '/');
        $this->token = config('services.aapanel.token', '');
    }

    /**
     * Build auth fields for POST body (aaPanel API format).
     */
    protected function authFields(): array
    {
        $requestTime = (string) time();

        return [
            'request_time' => $requestTime,
            'request_token' => md5($requestTime . md5($this->token)),
        ];
    }

    /**
     * Send a request to the aaPanel mail server plugin.
     *
     * @param string $method  Plugin method name (maps to s= URL param)
     * @param array  $params  Additional form fields
     */
    protected function request(string $method, array $params = []): array
    {
        $url = "{$this->baseUrl}/plugin?action=a&name=mail_sys&s={$method}";
        $postData = array_merge($this->authFields(), $params);

        try {
            $response = Http::asForm()
                ->withoutVerifying()
                ->timeout(30)
                ->post($url, $postData);

            Log::info('aaPanel Mail API Request', [
                'url' => $url,
                'method' => $method,
                'params' => array_diff_key($params, array_flip(['password'])),
                'status' => $response->status(),
                'body' => substr($response->body(), 0, 500),
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (is_null($data)) {
                    return [
                        'success' => false,
                        'error' => 'Non-JSON response: ' . substr($response->body(), 0, 300),
                    ];
                }

                if (isset($data['status']) && $data['status'] === false) {
                    return [
                        'success' => false,
                        'error' => $data['msg'] ?? 'Operation failed',
                        'data' => $data,
                    ];
                }

                return [
                    'success' => true,
                    'data' => $data,
                ];
            }

            return [
                'success' => false,
                'error' => 'HTTP ' . $response->status() . ': ' . substr($response->body(), 0, 300),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('aaPanel Mail API Error', [
                'url' => $url,
                'method' => $method,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send a request to a core aaPanel endpoint (not plugin).
     */
    protected function systemRequest(string $path): array
    {
        $url = "{$this->baseUrl}/{$path}";
        $postData = $this->authFields();

        try {
            $response = Http::asForm()
                ->withoutVerifying()
                ->timeout(15)
                ->post($url, $postData);

            return [
                'status' => $response->status(),
                'connected' => $response->successful(),
                'body' => substr($response->body(), 0, 500),
            ];
        } catch (\Exception $e) {
            return ['connected' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Create a new email account.
     * Plugin method: add_mailbox (assumed — verify with: grep 'add_mailbox\|add_mail\|create_mail' mail_sys_main.py)
     */
    public function createEmail(string $domain, string $username, string $password, int $quota = 1024): array
    {
        $result = $this->request('add_mailbox', [
            'username' => "{$username}@{$domain}",
            'password' => $password,
            'quota' => "{$quota} MB",
            'full_name' => $username,
        ]);

        if ($result['success']) {
            $result['message'] = "Email account {$username}@{$domain} created successfully";
        }

        return $result;
    }

    /**
     * List all email accounts for a domain.
     * Plugin method: get_mailboxs (line 2204 of mail_sys_main.py — note the typo is intentional).
     */
    public function listEmails(string $domain, int $page = 1, int $size = 100): array
    {
        return $this->request('get_mailboxs', [
            'domain' => $domain,
            'p' => $page,
            'size' => $size,
        ]);
    }

    /**
     * Delete an email account.
     * Plugin method: delete_mailbox (line 2822 of mail_sys_main.py).
     */
    public function deleteEmail(string $domain, string $username): array
    {
        $result = $this->request('delete_mailbox', [
            'domain' => $domain,
            'username' => "{$username}@{$domain}",
        ]);

        if ($result['success']) {
            $result['message'] = "Email account {$username}@{$domain} deleted successfully";
        }

        return $result;
    }

    /**
     * Change an email account password.
     * Plugin method: update_mailbox (line 2755 of mail_sys_main.py).
     * Requires: username, password, quota (e.g. "1024 MB"), full_name, active, is_admin.
     */
    public function changeEmailPassword(string $domain, string $username, string $newPassword): array
    {
        $fullEmail = "{$username}@{$domain}";

        // Fetch current mailbox info to preserve existing values
        $current = $this->listEmails($domain);
        $mailbox = null;
        if ($current['success'] && isset($current['data']['data'])) {
            foreach ($current['data']['data'] as $item) {
                if (($item['username'] ?? '') === $fullEmail) {
                    $mailbox = $item;
                    break;
                }
            }
        }

        // Convert quota bytes back to "X MB" format for the API
        $quotaBytes = $mailbox['quota'] ?? 1073741824;
        if ($quotaBytes >= 1073741824 && $quotaBytes % 1073741824 === 0) {
            $quotaStr = intval($quotaBytes / 1073741824) . ' GB';
        } else {
            $quotaStr = intval($quotaBytes / 1048576) . ' MB';
        }

        $result = $this->request('update_mailbox', [
            'username' => $fullEmail,
            'password' => $newPassword,
            'quota' => $quotaStr,
            'full_name' => $mailbox['full_name'] ?? $username,
            'active' => $mailbox['active'] ?? 1,
            'is_admin' => $mailbox['is_admin'] ?? 0,
        ]);

        if ($result['success']) {
            $result['message'] = 'Email password changed successfully';
        }

        return $result;
    }

    /**
     * Test API connection.
     * Tests both the core panel API and the mail plugin.
     */
    public function testConnection(): array
    {
        // 1. Test core panel API
        $systemResult = $this->systemRequest('system?action=GetSystemTotal');

        // 2. Test mail plugin — get_mailboxs at line 2204
        $mailResult = $this->request('get_mailboxs', [
            'domain' => 'ballie.co',
        ]);

        return [
            'success' => $systemResult['connected'] ?? false,
            'data' => [
                'system_test' => $systemResult,
                'mail_test' => $mailResult,
            ],
        ];
    }
}
