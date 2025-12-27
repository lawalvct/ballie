<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CyberPanelEmailService
{
    protected $apiUrl;
    protected $serverUserName;
    protected $serverPassword;
    protected $token;

    public function __construct()
    {
        $this->apiUrl = config('services.cyberpanel.api_url', 'https://staging.cyberpanel.net:8090/cloudAPI/');
        $this->serverUserName = config('services.cyberpanel.username', 'admin');
        $this->serverPassword = config('services.cyberpanel.password');
        $this->token = $this->generateToken();

        Log::info('CyberPanel Service Initialized', [
            'api_url' => $this->apiUrl,
            'username' => $this->serverUserName,
            'password_set' => !empty($this->serverPassword),
            'token' => $this->token,
        ]);
    }

    /**
     * Generate Basic Auth token for CyberPanel API
     *
     * @return string
     */
    protected function generateToken(): string
    {
        // Use temporary hardcoded token
        return "Basic 9b3e4b6f61736c065ccde13f2f1d601a4f1a504a56f045ea18276f2310da0609";
    }

    /**
     * Create a new email account
     *
     * @param string $domain
     * @param string $username
     * @param string $password
     * @return array
     */
    public function createEmail(string $domain, string $username, string $password): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
                'Content-Type' => 'application/json',
            ])
            ->withoutVerifying()
            ->timeout(30)
            ->post($this->apiUrl, [
                'serverUserName' => $this->serverUserName,
                'controller' => 'submitEmailCreation',
                'domain' => $domain,
                'username' => $username,
                'passwordByPass' => $password,
            ]);

            Log::info('CyberPanel Email Creation', [
                'domain' => $domain,
                'username' => $username,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'message' => 'Email account created successfully',
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error_message'] ?? 'Failed to create email account',
                'status' => $response->status(),
            ];

        } catch (\Exception $e) {
            Log::error('CyberPanel Email Creation Error', [
                'domain' => $domain,
                'username' => $username,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * List all email accounts for a domain
     *
     * @param string $domain
     * @return array
     */
    public function listEmails(string $domain): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
                'Content-Type' => 'application/json',
            ])
            ->withoutVerifying()
            ->timeout(30)
            ->post($this->apiUrl, [
                'serverUserName' => $this->serverUserName,
                'controller' => 'getEmailsForDomain',
                'domain' => $domain,
            ]);

            Log::info('CyberPanel Get Emails For Domain', [
                'domain' => $domain,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error_message'] ?? 'Failed to fetch emails',
            ];

        } catch (\Exception $e) {
            Log::error('CyberPanel List Emails Error', [
                'domain' => $domain,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Delete an email account
     *
     * @param string $email Full email address (e.g., username@ballie.co)
     * @return array
     */
    public function deleteEmail(string $email): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
                'Content-Type' => 'application/json',
            ])
            ->withoutVerifying()
            ->timeout(30)
            ->post($this->apiUrl, [
                'serverUserName' => $this->serverUserName,
                'controller' => 'submitEmailDeletion',
                'email' => $email,
            ]);

            Log::info('CyberPanel Email Deletion', [
                'email' => $email,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'message' => 'Email account deleted successfully',
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error_message'] ?? 'Failed to delete email account',
            ];

        } catch (\Exception $e) {
            Log::error('CyberPanel Email Deletion Error', [
                'domain' => $domain,
                'username' => $username,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Change email password
     *
     * @param string $domain
     * @param string $username
     * @param string $newPassword
     * @return array
     */
    public function changeEmailPassword(string $domain, string $username, string $newPassword): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
                'Content-Type' => 'application/json',
            ])
            ->withoutVerifying()
            ->timeout(30)
            ->post($this->apiUrl, [
                'serverUserName' => $this->serverUserName,
                'controller' => 'changeEmailPassword',
                'domain' => $domain,
                'username' => $username,
                'passwordByPass' => $newPassword,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'message' => 'Email password changed successfully',
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error_message'] ?? 'Failed to change email password',
            ];

        } catch (\Exception $e) {
            Log::error('CyberPanel Email Password Change Error', [
                'domain' => $domain,
                'username' => $username,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get list of available domains
     *
     * @return array
     */
    public function listDomains(): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
                'Content-Type' => 'application/json',
            ])
            ->withoutVerifying()
            ->timeout(30)
            ->post($this->apiUrl, [
                'serverUserName' => $this->serverUserName,
                'controller' => 'fetchDomains',
                'masterDomain' => 'ballie.co',
            ]);

            Log::info('CyberPanel Fetch Domains', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error_message'] ?? 'Failed to fetch domains',
            ];

        } catch (\Exception $e) {
            Log::error('CyberPanel List Domains Error', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create a server backup for a website
     *
     * @param string $website
     * @return array
     */
    public function createBackup(string $website): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
                'Content-Type' => 'application/json',
            ])
            ->withoutVerifying()
            ->timeout(60)
            ->post($this->apiUrl, [
                'serverUserName' => $this->serverUserName,
                'controller' => 'submitBackupCreation',
                'websiteToBeBacked' => $website,
            ]);

            Log::info('CyberPanel Backup Creation', [
                'website' => $website,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'message' => 'Backup creation initiated successfully',
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error_message'] ?? 'Failed to create backup',
            ];

        } catch (\Exception $e) {
            Log::error('CyberPanel Backup Creation Error', [
                'website' => $website,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
