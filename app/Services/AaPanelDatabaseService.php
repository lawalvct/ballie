<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AaPanelDatabaseService
{
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.aapanel.url', 'https://127.0.0.1:31947'), '/');
        $this->token = config('services.aapanel.token', '');
    }

    /**
     * Build auth fields for POST body.
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
     * Send a request to a core aaPanel endpoint.
     */
    protected function request(string $path, array $params = []): array
    {
        $url = "{$this->baseUrl}/{$path}";
        $postData = array_merge($this->authFields(), $params);

        try {
            $response = Http::asForm()
                ->withoutVerifying()
                ->timeout(120)
                ->post($url, $postData);

            Log::info('aaPanel Database API Request', [
                'url' => $url,
                'params' => array_diff_key($params, array_flip(['request_token'])),
                'status' => $response->status(),
                'body' => substr($response->body(), 0, 1000),
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
            Log::error('aaPanel Database API Error', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * List all databases.
     */
    public function listDatabases(int $page = 1, int $limit = 50): array
    {
        $result = $this->request('data?action=getData', [
            'table' => 'databases',
            'limit' => $limit,
            'p' => $page,
        ]);

        if ($result['success'] && isset($result['data']['data'])) {
            return [
                'success' => true,
                'databases' => $result['data']['data'],
                'total' => $result['data']['page']['total'] ?? count($result['data']['data']),
            ];
        }

        return $result;
    }

    /**
     * Create a database backup on the server.
     *
     * @param int $id Database ID from aaPanel
     */
    public function createBackup(int $id): array
    {
        return $this->request('database?action=ToBackup', [
            'id' => $id,
        ]);
    }

    /**
     * List backups for a specific database.
     *
     * @param int $id Database ID from aaPanel
     */
    public function listBackups(int $id, int $page = 1, int $limit = 20): array
    {
        $result = $this->request('data?action=getData', [
            'table' => 'backup',
            'limit' => $limit,
            'p' => $page,
            'type' => 1,
            'search' => $id,
        ]);

        if ($result['success'] && isset($result['data']['data'])) {
            return [
                'success' => true,
                'backups' => $result['data']['data'],
                'total' => $result['data']['page']['total'] ?? count($result['data']['data']),
            ];
        }

        return $result;
    }

    /**
     * Delete a specific backup file.
     *
     * @param int $id Backup record ID
     */
    public function deleteBackup(int $id): array
    {
        return $this->request('database?action=DelBackup', [
            'id' => $id,
        ]);
    }

    /**
     * Download a backup file URL.
     * Returns the download path on server.
     *
     * @param int $id Backup record ID
     */
    public function getBackupFileInfo(int $id): array
    {
        $result = $this->request('data?action=getData', [
            'table' => 'backup',
            'limit' => 1,
            'search' => $id,
            'type' => 1,
        ]);

        return $result;
    }

    /**
     * Test the database API connection.
     */
    public function testConnection(): array
    {
        return $this->listDatabases(1, 1);
    }
}
