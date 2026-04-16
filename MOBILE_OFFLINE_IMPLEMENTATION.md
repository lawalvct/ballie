# Ballie Mobile Offline Implementation Guide

## Overview
This document outlines the full offline-first architecture for the Ballie React Native mobile app, covering both **frontend (React Native)** and **backend (Laravel)** changes.

---

## Architecture Summary

```
┌─────────────────────┐         ┌──────────────────────┐
│   React Native App  │         │   Laravel Backend     │
│                     │         │                       │
│  ┌───────────────┐  │  sync   │  ┌─────────────────┐ │
│  │ WatermelonDB  │◄─┼────────►┼─►│ POST /api/sync  │ │
│  │ (Local DB)    │  │         │  └─────────────────┘ │
│  └───────────────┘  │         │                       │
│  ┌───────────────┐  │  cache  │  ┌─────────────────┐ │
│  │ React Query + │◄─┼────────►┼─►│ GET /api/*      │ │
│  │ AsyncStorage  │  │         │  └─────────────────┘ │
│  └───────────────┘  │         │                       │
│  ┌───────────────┐  │         │  ┌─────────────────┐ │
│  │ Offline Queue │──┼────────►┼─►│ Conflict Resolver│ │
│  │ (AsyncStorage)│  │  replay │  └─────────────────┘ │
│  └───────────────┘  │         │                       │
│  ┌───────────────┐  │         │                       │
│  │ NetInfo       │  │         │                       │
│  │ (Connectivity)│  │         │                       │
│  └───────────────┘  │         │                       │
└─────────────────────┘         └──────────────────────┘
```

---

## Part 1: React Native (Frontend)

### 1.1 Required Packages

```bash
npm install @nozbe/watermelondb
npm install @tanstack/react-query @tanstack/query-async-storage-persister
npm install @react-native-async-storage/async-storage
npm install @react-native-community/netinfo
```

### 1.2 Local Database — WatermelonDB

Use WatermelonDB for local persistent storage. It's optimized for React Native with lazy loading and fast queries.

**Define schema:**
```js
// src/db/schema.js
import { appSchema, tableSchema } from '@nozbe/watermelondb';

export default appSchema({
  version: 1,
  tables: [
    tableSchema({
      name: 'customers',
      columns: [
        { name: 'sync_id', type: 'string' },         // UUID from server
        { name: 'name', type: 'string' },
        { name: 'email', type: 'string', isOptional: true },
        { name: 'phone', type: 'string', isOptional: true },
        { name: 'balance', type: 'number' },
        { name: 'synced', type: 'boolean' },           // false = pending sync
        { name: 'client_updated_at', type: 'number' }, // timestamp
        { name: 'server_updated_at', type: 'number' },
        { name: 'is_deleted', type: 'boolean' },
      ],
    }),
    tableSchema({
      name: 'invoices',
      columns: [
        { name: 'sync_id', type: 'string' },
        { name: 'customer_sync_id', type: 'string' },
        { name: 'invoice_number', type: 'string' },
        { name: 'total', type: 'number' },
        { name: 'status', type: 'string' },
        { name: 'synced', type: 'boolean' },
        { name: 'client_updated_at', type: 'number' },
        { name: 'server_updated_at', type: 'number' },
        { name: 'is_deleted', type: 'boolean' },
      ],
    }),
    tableSchema({
      name: 'products',
      columns: [
        { name: 'sync_id', type: 'string' },
        { name: 'name', type: 'string' },
        { name: 'sku', type: 'string', isOptional: true },
        { name: 'price', type: 'number' },
        { name: 'stock_quantity', type: 'number' },
        { name: 'synced', type: 'boolean' },
        { name: 'client_updated_at', type: 'number' },
        { name: 'server_updated_at', type: 'number' },
        { name: 'is_deleted', type: 'boolean' },
      ],
    }),
    tableSchema({
      name: 'payments',
      columns: [
        { name: 'sync_id', type: 'string' },
        { name: 'invoice_sync_id', type: 'string' },
        { name: 'amount', type: 'number' },
        { name: 'method', type: 'string' },
        { name: 'synced', type: 'boolean' },
        { name: 'client_updated_at', type: 'number' },
        { name: 'server_updated_at', type: 'number' },
        { name: 'is_deleted', type: 'boolean' },
      ],
    }),
    tableSchema({
      name: 'sync_queue',
      columns: [
        { name: 'table_name', type: 'string' },
        { name: 'record_sync_id', type: 'string' },
        { name: 'action', type: 'string' },           // create | update | delete
        { name: 'payload', type: 'string' },           // JSON stringified data
        { name: 'client_updated_at', type: 'number' },
        { name: 'retries', type: 'number' },
        { name: 'status', type: 'string' },            // pending | syncing | failed
      ],
    }),
  ],
});
```

### 1.3 API Response Caching — React Query + AsyncStorage

```js
// src/api/queryClient.js
import { QueryClient } from '@tanstack/react-query';
import { createAsyncStoragePersister } from '@tanstack/query-async-storage-persister';
import AsyncStorage from '@react-native-async-storage/async-storage';

export const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      staleTime: 1000 * 60 * 5,  // 5 minutes
      cacheTime: 1000 * 60 * 60 * 24, // 24 hours
      retry: 2,
      networkMode: 'offlineFirst', // Use cache first, fetch in background
    },
  },
});

export const persister = createAsyncStoragePersister({
  storage: AsyncStorage,
  key: 'BALLIE_QUERY_CACHE',
});
```

### 1.4 Network Detection

```js
// src/hooks/useNetwork.js
import { useEffect, useState } from 'react';
import NetInfo from '@react-native-community/netinfo';

export function useNetwork() {
  const [isConnected, setIsConnected] = useState(true);

  useEffect(() => {
    const unsubscribe = NetInfo.addEventListener(state => {
      setIsConnected(state.isConnected && state.isInternetReachable);
    });
    return () => unsubscribe();
  }, []);

  return isConnected;
}
```

**Offline banner component:**
```jsx
// src/components/OfflineBanner.jsx
import { useNetwork } from '../hooks/useNetwork';

export function OfflineBanner() {
  const isConnected = useNetwork();

  if (isConnected) return null;

  return (
    <View style={{ backgroundColor: '#f59e0b', padding: 8, alignItems: 'center' }}>
      <Text style={{ color: '#fff', fontWeight: '600', fontSize: 13 }}>
        📶 You're offline — changes will sync when connected
      </Text>
    </View>
  );
}
```

### 1.5 Offline Action Queue

```js
// src/sync/offlineQueue.js
import AsyncStorage from '@react-native-async-storage/async-storage';
import { v4 as uuidv4 } from 'uuid';

const QUEUE_KEY = 'BALLIE_OFFLINE_QUEUE';

export async function enqueueAction(tableName, action, syncId, data) {
  const queue = JSON.parse(await AsyncStorage.getItem(QUEUE_KEY) || '[]');

  queue.push({
    id: uuidv4(),
    table: tableName,
    action,              // 'create' | 'update' | 'delete'
    sync_id: syncId,
    data,
    client_updated_at: new Date().toISOString(),
    retries: 0,
    status: 'pending',
  });

  await AsyncStorage.setItem(QUEUE_KEY, JSON.stringify(queue));
}

export async function getQueue() {
  return JSON.parse(await AsyncStorage.getItem(QUEUE_KEY) || '[]');
}

export async function clearQueue() {
  await AsyncStorage.removeItem(QUEUE_KEY);
}

export async function removeFromQueue(id) {
  const queue = await getQueue();
  const filtered = queue.filter(item => item.id !== id);
  await AsyncStorage.setItem(QUEUE_KEY, JSON.stringify(filtered));
}
```

### 1.6 Sync Service

```js
// src/sync/syncService.js
import NetInfo from '@react-native-community/netinfo';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { getQueue, clearQueue } from './offlineQueue';
import { api } from '../api/client';

const LAST_SYNC_KEY = 'BALLIE_LAST_SYNCED_AT';

export async function performSync() {
  const netState = await NetInfo.fetch();
  if (!netState.isConnected) return { success: false, reason: 'offline' };

  const lastSyncedAt = await AsyncStorage.getItem(LAST_SYNC_KEY) || '1970-01-01T00:00:00Z';
  const offlineChanges = await getQueue();

  try {
    const response = await api.post('/mobile/sync', {
      last_synced_at: lastSyncedAt,
      changes: offlineChanges.filter(c => c.status === 'pending'),
    });

    const { server_changes, conflicts, synced_at } = response.data;

    // Apply server changes to local WatermelonDB
    await applyServerChanges(server_changes);

    // Handle conflicts (log or prompt user)
    if (conflicts.length > 0) {
      await handleConflicts(conflicts);
    }

    // Clear successfully synced items from queue
    await clearQueue();

    // Store last sync timestamp
    await AsyncStorage.setItem(LAST_SYNC_KEY, synced_at);

    return { success: true, synced_at };
  } catch (error) {
    console.error('Sync failed:', error);
    return { success: false, reason: error.message };
  }
}

async function applyServerChanges(changes) {
  // Apply to WatermelonDB using batch writes
  // For each table (customers, invoices, products, payments):
  //   - Insert new records
  //   - Update existing records
  //   - Mark deleted records (soft delete locally)
}

async function handleConflicts(conflicts) {
  // Strategy: For now, server-wins for financial data
  // Log conflicts for debugging
  console.warn('Sync conflicts:', conflicts);
}

// Auto-sync when coming back online
NetInfo.addEventListener(state => {
  if (state.isConnected && state.isInternetReachable) {
    performSync();
  }
});
```

### 1.7 Usage Pattern in Components

```jsx
// Example: Creating an invoice offline
import { enqueueAction } from '../sync/offlineQueue';
import { useNetwork } from '../hooks/useNetwork';
import { v4 as uuidv4 } from 'uuid';

function CreateInvoice() {
  const isConnected = useNetwork();

  const handleCreateInvoice = async (invoiceData) => {
    const syncId = uuidv4(); // Generate UUID locally

    // 1. Save to local DB immediately (instant UI)
    await database.write(async () => {
      await database.get('invoices').create(record => {
        record.syncId = syncId;
        record.customerSyncId = invoiceData.customer_id;
        record.total = invoiceData.total;
        record.status = 'draft';
        record.synced = false;
        record.clientUpdatedAt = Date.now();
      });
    });

    if (isConnected) {
      // 2a. Online: Send to server immediately
      try {
        await api.post('/invoices', { ...invoiceData, sync_id: syncId });
        // Mark as synced in local DB
      } catch (e) {
        // Failed — queue for later
        await enqueueAction('invoices', 'create', syncId, invoiceData);
      }
    } else {
      // 2b. Offline: Queue for sync later
      await enqueueAction('invoices', 'create', syncId, invoiceData);
    }
  };
}
```

---

## Part 2: Laravel Backend

### 2.1 Database Migrations

**Add `sync_id` and `client_updated_at` to all syncable tables:**

```bash
php artisan make:migration add_sync_columns_to_syncable_tables
```

```php
// database/migrations/xxxx_add_sync_columns_to_syncable_tables.php
public function up()
{
    $tables = ['customers', 'invoices', 'products', 'payments', 'invoice_items'];

    foreach ($tables as $table) {
        Schema::table($table, function (Blueprint $table) {
            $table->uuid('sync_id')->unique()->nullable()->after('id');
            $table->timestamp('client_updated_at')->nullable()->after('updated_at');
        });
    }
}

public function down()
{
    $tables = ['customers', 'invoices', 'products', 'payments', 'invoice_items'];

    foreach ($tables as $table) {
        Schema::table($table, function (Blueprint $table) {
            $table->dropColumn(['sync_id', 'client_updated_at']);
        });
    }
}
```

**Ensure soft deletes are enabled on all syncable models:**

```php
// app/Models/Customer.php (and Invoice, Product, Payment, etc.)
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        // ... existing fields
        'sync_id',
        'client_updated_at',
    ];
}
```

### 2.2 Sync Controller

```bash
php artisan make:controller Tenant/Api/MobileSyncController
```

```php
// app/Http/Controllers/Tenant/Api/MobileSyncController.php
<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MobileSyncController extends Controller
{
    /**
     * Syncable models mapping
     */
    private array $syncableModels = [
        'customers' => Customer::class,
        'invoices'  => Invoice::class,
        'products'  => Product::class,
        'payments'  => Payment::class,
    ];

    /**
     * Conflict resolution strategy per table
     * server-wins: Server version always takes precedence
     * client-wins: Client version takes precedence
     * last-write-wins: Most recent timestamp wins
     */
    private array $conflictStrategy = [
        'customers' => 'last-write-wins',
        'invoices'  => 'server-wins',   // Financial data = server authority
        'products'  => 'last-write-wins',
        'payments'  => 'server-wins',   // Financial data = server authority
    ];

    /**
     * Bidirectional sync endpoint
     * POST /api/{tenant}/mobile/sync
     */
    public function sync(Request $request)
    {
        $request->validate([
            'last_synced_at' => 'required|date',
            'changes'        => 'array',
            'changes.*.table'              => 'required|string|in:customers,invoices,products,payments',
            'changes.*.action'             => 'required|string|in:create,update,delete',
            'changes.*.sync_id'            => 'required|uuid',
            'changes.*.data'               => 'required_unless:changes.*.action,delete|array',
            'changes.*.client_updated_at'  => 'required|date',
        ]);

        $lastSyncedAt = $request->input('last_synced_at');
        $clientChanges = $request->input('changes', []);

        // 1. Process client changes
        $conflicts = [];
        $applied = [];

        foreach ($clientChanges as $change) {
            $result = $this->processClientChange($change);

            if ($result['status'] === 'conflict') {
                $conflicts[] = $result;
            } else {
                $applied[] = $result;
            }
        }

        // 2. Gather server changes since last sync
        $serverChanges = $this->getServerChanges($lastSyncedAt);

        // 3. Return response
        return response()->json([
            'server_changes' => $serverChanges,
            'conflicts'      => $conflicts,
            'applied'        => count($applied),
            'synced_at'      => now()->toISOString(),
        ]);
    }

    /**
     * Initial sync — paginated bulk download for first-time setup
     * GET /api/{tenant}/mobile/sync/initial
     */
    public function initialSync(Request $request)
    {
        $request->validate([
            'table'    => 'required|string|in:customers,invoices,products,payments',
            'page'     => 'integer|min:1',
            'per_page' => 'integer|min:50|max:1000',
        ]);

        $modelClass = $this->syncableModels[$request->input('table')];
        $perPage = $request->input('per_page', 500);

        $data = $modelClass::withTrashed()
            ->orderBy('id')
            ->paginate($perPage);

        return response()->json([
            'table'        => $request->input('table'),
            'data'         => $data->items(),
            'current_page' => $data->currentPage(),
            'last_page'    => $data->lastPage(),
            'total'        => $data->total(),
            'synced_at'    => now()->toISOString(),
        ]);
    }

    /**
     * Process a single client change
     */
    private function processClientChange(array $change): array
    {
        $modelClass = $this->syncableModels[$change['table']];
        $syncId = $change['sync_id'];
        $action = $change['action'];
        $data = $change['data'] ?? [];
        $clientUpdatedAt = $change['client_updated_at'];

        $existing = $modelClass::withTrashed()
            ->where('sync_id', $syncId)
            ->first();

        // CREATE
        if ($action === 'create') {
            if ($existing) {
                return [
                    'status'  => 'skipped',
                    'sync_id' => $syncId,
                    'reason'  => 'already_exists',
                ];
            }

            $record = $modelClass::create(array_merge($data, [
                'sync_id'           => $syncId,
                'client_updated_at' => $clientUpdatedAt,
            ]));

            return [
                'status'  => 'created',
                'sync_id' => $syncId,
                'id'      => $record->id,
            ];
        }

        // UPDATE
        if ($action === 'update') {
            if (!$existing) {
                return [
                    'status'  => 'error',
                    'sync_id' => $syncId,
                    'reason'  => 'not_found',
                ];
            }

            // Check for conflict
            if ($existing->updated_at > $clientUpdatedAt) {
                $strategy = $this->conflictStrategy[$change['table']] ?? 'server-wins';

                if ($strategy === 'server-wins') {
                    return [
                        'status'      => 'conflict',
                        'sync_id'     => $syncId,
                        'strategy'    => 'server-wins',
                        'server_data' => $existing->toArray(),
                    ];
                }

                if ($strategy === 'last-write-wins') {
                    if ($existing->updated_at > $clientUpdatedAt) {
                        return [
                            'status'      => 'conflict',
                            'sync_id'     => $syncId,
                            'strategy'    => 'last-write-wins',
                            'winner'      => 'server',
                            'server_data' => $existing->toArray(),
                        ];
                    }
                    // else: client is newer, apply below
                }
            }

            $existing->update(array_merge($data, [
                'client_updated_at' => $clientUpdatedAt,
            ]));

            return [
                'status'  => 'updated',
                'sync_id' => $syncId,
            ];
        }

        // DELETE
        if ($action === 'delete') {
            if ($existing) {
                $existing->delete(); // Soft delete
            }

            return [
                'status'  => 'deleted',
                'sync_id' => $syncId,
            ];
        }

        return ['status' => 'unknown_action', 'sync_id' => $syncId];
    }

    /**
     * Get all server-side changes since a given timestamp
     */
    private function getServerChanges(string $since): array
    {
        $changes = [];

        foreach ($this->syncableModels as $table => $modelClass) {
            $changes[$table] = $modelClass::withTrashed()
                ->where('updated_at', '>', $since)
                ->select(['id', 'sync_id', 'deleted_at', 'updated_at', 'client_updated_at'])
                ->with([]) // Add any needed relationships
                ->get()
                ->map(function ($record) {
                    $data = $record->toArray();
                    $data['_action'] = $record->trashed() ? 'delete' : 'upsert';
                    return $data;
                });
        }

        return $changes;
    }
}
```

### 2.3 Routes

```php
// routes/api.php (inside tenant-scoped group)

Route::prefix('{tenant}/mobile')->middleware(['auth:sanctum'])->group(function () {
    Route::post('/sync', [MobileSyncController::class, 'sync']);
    Route::get('/sync/initial', [MobileSyncController::class, 'initialSync']);
});
```

### 2.4 Model Trait for Sync Support

```php
// app/Traits/Syncable.php
<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait Syncable
{
    protected static function bootSyncable(): void
    {
        static::creating(function ($model) {
            if (empty($model->sync_id)) {
                $model->sync_id = Str::uuid()->toString();
            }
        });
    }

    public function scopeSyncedAfter($query, string $timestamp)
    {
        return $query->withTrashed()->where('updated_at', '>', $timestamp);
    }
}
```

**Apply to models:**
```php
// app/Models/Customer.php
use App\Traits\Syncable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes, Syncable;
}
```

### 2.5 Rate Limiting

```php
// app/Providers/RouteServiceProvider.php (or bootstrap/app.php)
RateLimiter::for('mobile-sync', function (Request $request) {
    return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
});
```

Apply to sync routes:
```php
Route::post('/sync', [MobileSyncController::class, 'sync'])
    ->middleware('throttle:mobile-sync');
```

### 2.6 Response Compression

Ensure `gzip` is enabled in your web server (Nginx/Apache) for JSON responses. For Laravel-level compression:

```php
// Add to .env
RESPONSE_COMPRESSION=true
```

Or use middleware:
```php
// app/Http/Middleware/CompressResponse.php
public function handle($request, Closure $next)
{
    $response = $next($request);

    if ($request->header('Accept-Encoding') &&
        str_contains($request->header('Accept-Encoding'), 'gzip')) {
        $response->header('Content-Encoding', 'gzip');
        $response->setContent(gzencode($response->getContent(), 9));
    }

    return $response;
}
```

---

## Part 3: Conflict Resolution Strategies

| Table      | Strategy        | Reason                                           |
|------------|-----------------|--------------------------------------------------|
| customers  | last-write-wins | Non-financial, safe for either side to win        |
| invoices   | server-wins     | Financial integrity — server is source of truth   |
| products   | last-write-wins | Stock may change from either side                 |
| payments   | server-wins     | Financial integrity — never trust offline payments |
| ledger     | server-wins     | Accounting data must always be server-authoritative|

### Conflict Flow:
1. Client sends queued changes with `client_updated_at` timestamp
2. Server compares with `updated_at` on the record
3. If server record is newer → conflict detected
4. Strategy determines outcome:
   - **server-wins**: Return server data, client must overwrite local
   - **last-write-wins**: Compare timestamps, most recent wins
   - **client-wins**: Apply client data regardless (use sparingly)
5. Conflicts are returned in sync response for the app to handle

---

## Part 4: Data Flow Summary

### Online Mode
```
User Action → API Call → Server DB → Response → Update Local DB
```

### Offline Mode
```
User Action → Save to Local DB → Add to Offline Queue
                                         ↓
                              (When back online)
                                         ↓
                              Replay Queue → POST /sync → Handle Conflicts → Clear Queue
```

### Background Sync (Periodic)
```
Timer (every 5 min) OR Network Change → Check connectivity → POST /sync
```

---

## Part 5: Implementation Checklist

### Backend (Laravel)
- [ ] Run migration: add `sync_id` + `client_updated_at` to syncable tables
- [ ] Enable `SoftDeletes` on Customer, Invoice, Product, Payment models
- [ ] Add `Syncable` trait to all syncable models
- [ ] Create `MobileSyncController` with `sync()` and `initialSync()`
- [ ] Register routes in `routes/api.php`
- [ ] Add rate limiting for sync endpoint
- [ ] Enable gzip compression for API responses
- [ ] Backfill `sync_id` for existing records: `UPDATE customers SET sync_id = UUID() WHERE sync_id IS NULL`

### Frontend (React Native)
- [ ] Install WatermelonDB, React Query, AsyncStorage, NetInfo
- [ ] Define WatermelonDB schema matching server tables
- [ ] Set up React Query with AsyncStorage persistence
- [ ] Create `useNetwork()` hook + `OfflineBanner` component
- [ ] Implement offline queue (enqueue/dequeue/clear)
- [ ] Build sync service with auto-sync on reconnect
- [ ] Add UUID generation for offline record creation
- [ ] Handle conflict responses from server
- [ ] Add periodic background sync (every 5 minutes)
- [ ] Test: create invoice offline → go online → verify sync

---

## Part 6: Testing Scenarios

| Scenario | Expected Behavior |
|----------|-------------------|
| Create invoice while offline | Saved locally, queued, synced on reconnect |
| Edit customer while offline | Updated locally, queued, synced on reconnect |
| Two devices edit same customer | Conflict detected, resolved per strategy |
| Delete product while offline | Soft-deleted locally, synced on reconnect |
| First app install | Initial sync downloads all data in pages |
| Server down during sync | Queue preserved, retried next time |
| Large dataset sync | Paginated, gzipped, chunked |

---

## File Locations (After Implementation)

### Laravel
```
app/Http/Controllers/Tenant/Api/MobileSyncController.php
app/Traits/Syncable.php
database/migrations/xxxx_add_sync_columns_to_syncable_tables.php
```

### React Native
```
src/db/schema.js
src/api/queryClient.js
src/hooks/useNetwork.js
src/components/OfflineBanner.jsx
src/sync/offlineQueue.js
src/sync/syncService.js
```
