<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttributeValue extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'attribute_id',
        'value',
        'value_type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'array',
    ];

    /**
     * Get the product detail that owns the attribute value.
     */
    public function productDetail(): BelongsTo
    {
        return $this->belongsTo(ProductDetail::class);
    }
    
    /**
     * Get the product that owns the attribute value through product detail.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id', 'productDetail');
    }

    /**
     * Get the attribute that owns the attribute value.
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * Get the actual value with the correct type casting.
     *
     * @return mixed
     */
    public function getValueAttribute($value)
    {
        if ($this->value_type === 'array' || $this->value_type === 'json') {
            return json_decode($value, true);
        }

        if ($this->value_type === 'integer') {
            return (int) $value;
        }

        if ($this->value_type === 'float') {
            return (float) $value;
        }

        if ($this->value_type === 'boolean') {
            return (bool) $value;
        }

        return $value;
    }
}
