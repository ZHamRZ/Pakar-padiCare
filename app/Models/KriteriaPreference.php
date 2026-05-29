<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KriteriaPreference extends Model
{
    protected $table = 'kriteria_preferences';

    protected $fillable = ['key', 'label', 'group', 'value', 'description'];

    protected $casts = [
        'value' => 'array',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        $pref = static::where('key', $key)->first();

        return $pref ? ($pref->value ?? $default) : $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public static function getByGroup(string $group): array
    {
        return static::where('group', $group)
            ->get()
            ->mapWithKeys(fn ($pref) => [$pref->key => $pref->value])
            ->all();
    }
}
