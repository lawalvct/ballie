<?php

namespace App\Services\MobileSync;

use App\Models\User;
use Illuminate\Support\Arr;

/**
 * Read-only access to the mobile sync registry.
 *
 * Wraps `config('mobile_sync.registry')` and applies the current
 * user's permissions so the rest of the sync pipeline never has to
 * recompute "what can this user actually pull/push?".
 */
class SyncRegistry
{
    /** @var array<string, array<string, mixed>> */
    private array $registry;

    public function __construct(?array $registry = null)
    {
        $this->registry = $registry ?? config('mobile_sync.registry', []);
    }

    /**
     * @return array<string>
     */
    public function tables(): array
    {
        return array_keys($this->registry);
    }

    public function has(string $table): bool
    {
        return array_key_exists($table, $this->registry);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function get(string $table): ?array
    {
        return $this->registry[$table] ?? null;
    }

    /**
     * Tables the user is allowed to pull, in dependency order.
     *
     * @return array<int, string>
     */
    public function pullableTablesFor(User $user): array
    {
        $allowed = [];

        foreach ($this->registry as $table => $def) {
            if (!Arr::get($def, 'pullable', false)) {
                continue;
            }
            if (!$this->userHasPermission($user, Arr::get($def, 'permissions.pull'))) {
                continue;
            }
            $allowed[] = $table;
        }

        return $this->sortByDependencies($allowed);
    }

    /**
     * @return array<int, string>
     */
    public function pushableTablesFor(User $user): array
    {
        $allowed = [];

        foreach ($this->registry as $table => $def) {
            if (!Arr::get($def, 'pushable', false)) {
                continue;
            }
            if (!$this->userHasPermission($user, Arr::get($def, 'permissions.push'))) {
                continue;
            }
            $allowed[] = $table;
        }

        return $this->sortByDependencies($allowed);
    }

    public function canPull(User $user, string $table): bool
    {
        $def = $this->get($table);
        if (!$def || !Arr::get($def, 'pullable', false)) {
            return false;
        }
        return $this->userHasPermission($user, Arr::get($def, 'permissions.pull'));
    }

    public function canPush(User $user, string $table, string $action): bool
    {
        $def = $this->get($table);
        if (!$def || !Arr::get($def, 'pushable', false)) {
            return false;
        }

        $allowedActions = (array) Arr::get($def, 'allowed_actions', ['create', 'update', 'delete']);
        if (!in_array($action, $allowedActions, true)) {
            return false;
        }

        return $this->userHasPermission($user, Arr::get($def, 'permissions.push'));
    }

    /**
     * Public attribute whitelist for a table; falls back to all
     * attributes if not configured.
     *
     * @return array<int, string>|null
     */
    public function publicAttributes(string $table): ?array
    {
        $def = $this->get($table);
        if (!$def) {
            return null;
        }
        return Arr::get($def, 'public_attributes');
    }

    /**
     * Strips attributes the client must never write directly.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function stripProtectedAttributes(string $table, array $payload): array
    {
        $protected = (array) Arr::get($this->get($table) ?? [], 'protected_attributes', []);
        $protected = array_merge($protected, [
            'id', 'tenant_id', 'created_by', 'updated_by', 'deleted_by',
            'server_version', 'created_at', 'updated_at', 'deleted_at',
        ]);

        return Arr::except($payload, $protected);
    }

    private function userHasPermission(User $user, ?string $permission): bool
    {
        if ($permission === null || $permission === '') {
            return true;
        }

        if (method_exists($user, 'hasPermission')) {
            return (bool) $user->hasPermission($permission);
        }

        return false;
    }

    /**
     * Topological sort by `dependencies` so callers always pull
     * parent tables first.
     *
     * @param  array<int, string>  $tables
     * @return array<int, string>
     */
    private function sortByDependencies(array $tables): array
    {
        $sorted = [];
        $visited = [];
        $tableSet = array_flip($tables);

        $visit = function (string $table) use (&$visit, &$sorted, &$visited, $tableSet) {
            if (isset($visited[$table])) {
                return;
            }
            $visited[$table] = true;
            $deps = (array) Arr::get($this->registry[$table] ?? [], 'dependencies', []);
            foreach ($deps as $dep) {
                if (isset($tableSet[$dep])) {
                    $visit($dep);
                }
            }
            $sorted[] = $table;
        };

        foreach ($tables as $table) {
            $visit($table);
        }

        return $sorted;
    }
}
