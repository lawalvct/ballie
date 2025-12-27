<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\CyberPanelEmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use ZipArchive;

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
        try {
            $timestamp = now()->format('Y-m-d_His');
            $backupName = "ballie_backup_{$timestamp}";

            // Clear telescope tables before backup
            try {
                if (DB::getSchemaBuilder()->hasTable('telescope_entries')) {
                    DB::table('telescope_entries')->truncate();
                    Log::info('Telescope entries table cleared before backup');
                }
                if (DB::getSchemaBuilder()->hasTable('telescope_entries_tags')) {
                    DB::table('telescope_entries_tags')->truncate();
                    Log::info('Telescope entries tags table cleared before backup');
                }
            } catch (\Exception $e) {
                Log::warning('Failed to clear telescope tables', ['error' => $e->getMessage()]);
            }

            // Create temporary directory for backup files
            $tempDir = storage_path("app/temp-backup-{$timestamp}");
            if (!File::exists($tempDir)) {
                File::makeDirectory($tempDir, 0755, true);
            }

            // 1. Export Main Database
            $mainDatabase = env('DB_DATABASE', 'ballie');
            $this->exportDatabase($tempDir, $mainDatabase);

            // 2. Get tenant count (if tenants table exists)
            $tenantCount = 0;
            try {
                if (DB::getSchemaBuilder()->hasTable('tenants')) {
                    $tenants = DB::table('tenants')->get();
                    $tenantCount = $tenants->count();

                    // Export each tenant database if they exist
                    foreach ($tenants as $tenant) {
                        $tenantDb = 'tenant_' . $tenant->id;
                        if (DB::getSchemaBuilder()->hasTable($tenantDb)) {
                            $this->exportDatabase($tempDir, $tenantDb, true);
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Tenant database export skipped', ['error' => $e->getMessage()]);
            }

            // 3. Create backup info file
            $infoFile = $tempDir . '/backup_info.txt';
            $info = "Ballie Backup\n";
            $info .= "Created: " . now()->format('Y-m-d H:i:s') . "\n";
            $info .= "Type: Local Backup\n";
            $info .= "Domain: ballie.co\n";
            $info .= "Main Database: {$mainDatabase}\n";
            $info .= "Tenant Databases: {$tenantCount} included\n";
            File::put($infoFile, $info);

            // 4. Create ZIP archive
            $zipPath = storage_path("app/{$backupName}.zip");
            $zip = new ZipArchive();

            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                // Add all files from temp directory to zip
                $files = File::allFiles($tempDir);
                foreach ($files as $file) {
                    $relativePath = str_replace($tempDir . '/', '', $file->getPathname());
                    $zip->addFile($file->getPathname(), $relativePath);
                }
                $zip->close();
            } else {
                throw new \Exception('Failed to create ZIP archive');
            }

            // 5. Clean up temporary directory
            File::deleteDirectory($tempDir);

            // 6. Download the backup file
            Log::info('Local backup created successfully', [
                'backup_name' => $backupName,
                'file_size' => File::size($zipPath),
            ]);

            return response()->download($zipPath, $backupName . '.zip')->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Local backup creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Clean up on error
            if (isset($tempDir) && File::exists($tempDir)) {
                File::deleteDirectory($tempDir);
            }
            if (isset($zipPath) && File::exists($zipPath)) {
                File::delete($zipPath);
            }

            return back()->with('error', 'Failed to create local backup: ' . $e->getMessage());
        }
    }

    /**
     * Export a database to SQL file
     */
    protected function exportDatabase($directory, $databaseName, $isTenant = false)
    {
        try {
            // Use default mysql connection
            $connection = 'mysql';

            // Temporarily switch database for the connection
            $originalDatabase = config('database.connections.mysql.database');
            config(['database.connections.mysql.database' => $databaseName]);
            DB::purge('mysql');
            DB::reconnect('mysql');

            // Get all tables
            $tables = DB::connection($connection)->select('SHOW TABLES');
            $dbKey = 'Tables_in_' . $databaseName;

            $sql = "-- Ballie Database Backup\n";
            $sql .= "-- Database: " . $databaseName . "\n";
            $sql .= "-- Created: " . now()->format('Y-m-d H:i:s') . "\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            // Export each table
            foreach ($tables as $table) {
                $tableName = $table->$dbKey;

                // Get CREATE TABLE statement
                $createTable = DB::connection($connection)->select("SHOW CREATE TABLE `{$tableName}`");
                $sql .= "\n-- Table: {$tableName}\n";
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sql .= $createTable[0]->{'Create Table'} . ";\n\n";

                // Get table data
                $rows = DB::connection($connection)->table($tableName)->get();
                if ($rows->count() > 0) {
                    $sql .= "-- Data for table {$tableName}\n";
                    foreach ($rows as $row) {
                        $values = [];
                        foreach ((array)$row as $value) {
                            if (is_null($value)) {
                                $values[] = 'NULL';
                            } else {
                                $values[] = "'" . addslashes($value) . "'";
                            }
                        }
                        $sql .= "INSERT INTO `{$tableName}` VALUES (" . implode(', ', $values) . ");\n";
                    }
                    $sql .= "\n";
                }
            }

            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

            // Save SQL file
            $filename = $isTenant ? "{$databaseName}.sql" : "{$databaseName}.sql";
            File::put($directory . '/' . $filename, $sql);

            // Restore original database connection
            config(['database.connections.mysql.database' => $originalDatabase]);
            DB::purge('mysql');
            DB::reconnect('mysql');

            Log::info('Database exported', [
                'database' => $databaseName,
                'is_tenant' => $isTenant,
                'file' => $filename,
            ]);

        } catch (\Exception $e) {
            // Restore original database connection on error
            if (isset($originalDatabase)) {
                config(['database.connections.mysql.database' => $originalDatabase]);
                DB::purge('mysql');
                DB::reconnect('mysql');
            }

            Log::error('Database export failed', [
                'database' => $databaseName,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
