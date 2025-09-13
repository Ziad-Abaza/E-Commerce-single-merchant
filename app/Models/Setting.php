<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'options',
        'is_public',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
        'is_public' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get setting value with type casting
     */
    public function getTypedValueAttribute()
    {
        return match ($this->type) {
            'boolean' => (bool) $this->value,
            'number' => is_numeric($this->value) ? (float) $this->value : 0,
            'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }

    /**
     * Set value with proper type handling
     */
    public function setValueAttribute($value)
    {
        $this->attributes['value'] = match ($this->type) {
            'boolean' => $value ? '1' : '0',
            'json' => is_array($value) ? json_encode($value) : $value,
            default => $value,
        };
    }

    /**
     * Scope to get settings by group
     */
    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group)->orderBy('sort_order');
    }

    /**
     * Scope to get public settings
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Get setting by key
     */
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->typed_value : $default;
    }

    /**
     * Set setting value
     */
    public static function set($key, $value, $type = 'text')
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        );
    }
}
