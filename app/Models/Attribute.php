<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Attribute extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'type',
        'options',
        'is_required',
        'is_filterable',
        'is_variant',
        'is_visible_on_frontend',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'is_filterable' => 'boolean',
        'is_visible_on_frontend' => 'boolean',
    ];

    /**
     * Get the categories that have this attribute.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)
            ->withPivot(['is_required', 'sort_order'])
            ->withTimestamps();
    }

    /**
     * Get the attribute values for the attribute.
     */
    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }
}
