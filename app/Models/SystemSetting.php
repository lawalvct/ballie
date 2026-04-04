<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $fillable = ['group', 'key', 'value', 'type'];

    /**
     * Get a single setting value by key.
     */
    public static function getValue(string $key, $default = null)
    {
        $setting = Cache::rememberForever("system_setting.{$key}", function () use ($key) {
            return self::where('key', $key)->first();
        });

        if (!$setting) {
            return $default;
        }

        return self::castValue($setting->value, $setting->type);
    }

    /**
     * Set a single setting value by key.
     */
    public static function setValue(string $key, $value, string $group = null): void
    {
        $data = ['value' => (string) $value];
        if ($group) {
            $data['group'] = $group;
        }

        self::updateOrCreate(['key' => $key], $data);
        Cache::forget("system_setting.{$key}");
        Cache::forget("system_settings.group.{$group}");
    }

    /**
     * Get all settings for a group.
     */
    public static function getGroup(string $group): array
    {
        return Cache::rememberForever("system_settings.group.{$group}", function () use ($group) {
            $settings = self::where('group', $group)->get();
            $result = [];
            foreach ($settings as $setting) {
                $result[$setting->key] = self::castValue($setting->value, $setting->type);
            }
            return $result;
        });
    }

    /**
     * Get all settings keyed by key.
     */
    public static function getAllGrouped(): array
    {
        $settings = self::all();
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->group][$setting->key] = self::castValue($setting->value, $setting->type);
        }
        return $result;
    }

    /**
     * Bulk update settings from form data.
     */
    public static function bulkUpdate(array $data): void
    {
        foreach ($data as $key => $value) {
            $setting = self::where('key', $key)->first();
            if ($setting) {
                $setting->update(['value' => (string) $value]);
                Cache::forget("system_setting.{$key}");
                Cache::forget("system_settings.group.{$setting->group}");
            }
        }
    }

    /**
     * Cast value based on type.
     */
    protected static function castValue($value, string $type)
    {
        return match ($type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }
}
