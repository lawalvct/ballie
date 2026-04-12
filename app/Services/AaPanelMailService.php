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
        $this->baseUrl = rtrim(config('services.aapanel.url', 'https://102.68.84.38:8888'), '/');
        $this->token = config('services.aapanel.token', '');
    }

    /**
     * Build request headers with timestamp and signature.
     */
    protected function buildHeaders(): array
    {
        $requestTime = (string) time();

        return [
            'Content-Type' => 'application/json',
            'Request-Time' => $requestTime,
            'Signature' => md5($requestTime . $this->token),
        ];
    }

    /**
     * Send a request to the aaPanel mail server plugin.
     */
    protected function request(array $params): array
    {
        try {
            $response = Http::withHeaders($this->buildHeaders())
                ->withoutVerifying()
                ->timeout(30)
                ->post("{$this->baseUrl}/plugin?action=a&s=mail_sys&name=mail_sys", $params);

            Log::info('aaPanel Mail API Request', [
                'params' => array_diff_key($params, array_flip(['password'])),
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // aaPanel typically returns status true/false
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
                'error' => $response->json()['msg'] ?? 'Request failed with status ' . $response->status(),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('aaPanel Mail API Error', [
                'params' => array_diff_key($params, array_flip(['password'])),
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create a new email account.
     */
    public function createEmail(string $domain, string $username, string $password, int $quota = 1024): array
    {
        $result = $this->request([
            'action' => 'add_mailbox',
            'domain' => $domain,
            'username' => $username,
            'password' => $password,
            'quota' => $quota,
        ]);

        if ($result['success']) {
            $result['message'] = "Email account {$username}@{$domain} created successfully";
        }

        return $result;
    }

    /**
     * List all email accounts for a domain.
     */
    public function listEmails(string $domain): array
    {
        return $this->request([
            'action' => 'get_mailbox_list',
            'domain' => $domain,
        ]);
    }

    /**
     * Delete an email account.
     */
    public function deleteEmail(string $domain, string $username): array
    {
        $result = $this->request([
            'action' => 'del_mailbox',
            'domain' => $domain,
            'username' => $username,
        ]);

        if ($result['success']) {
            $result['message'] = "Email account {$username}@{$domain} deleted successfully";
        }

        return $result;
    }

    /**
     * Change an email account password.
     */
    public function changeEmailPassword(string $domain, string $username, string $newPassword): array
    {
        $result = $this->request([
            'action' => 'reset_password',
            'domain' => $domain,
            'username' => $username,
            'password' => $newPassword,
        ]);

        if ($result['success']) {
            $result['message'] = 'Email password changed successfully';
        }

        return $result;
    }

    /**
     * Test API connection.
     */
    public function testConnection(): array
    {
        return $this->request([
            'action' => 'get_mailbox_list',
            'domain' => 'ballie.co',
        ]);
    }
}
