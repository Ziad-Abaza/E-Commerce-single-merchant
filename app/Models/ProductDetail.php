<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductDetail extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'product_id',
        'size',
        'color',
        'material',
        'weight',
        'length',
        'width',
        'height',
        'origin',
        'quality',
        'packaging',
        'price',
        'discount',
        'stock',
        'min_stock_alert',
        'sku_variant',
        'barcode',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'product_id' => 'integer',
        'size' => 'string',
        'color' => 'string',
        'material' => 'string',
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'origin' => 'string',
        'quality' => 'string',
        'packaging' => 'string',
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'stock' => 'integer',
        'min_stock_alert' => 'integer',
        'sku_variant' => 'string',
        'barcode' => 'string',
        'is_active' => 'boolean',
    ];

    // ------------------------------
    // Media Collections
    // ------------------------------

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useDisk('public')
            ->withResponsiveImages();
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(100)
            ->height(100)
            ->sharpen(10)
            ->nonQueued();

        $this->addMediaConversion('medium')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->nonQueued();

        $this->addMediaConversion('large')
            ->width(600)
            ->height(600)
            ->sharpen(10)
            ->nonQueued();
    }

    // ------------------------------
    // Relations
    // ------------------------------

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(Cart::class, 'product_detail_id');
    }

    // ------------------------------
    // Scopes
    // ------------------------------

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'min_stock_alert');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stock', '<=', 0);
    }

    // ------------------------------
    // Attributes & Getters
    // ------------------------------

    /**
     * Get all images URLs
     */
    public function getImagesUrl()
    {
        return $this->getMedia('images')->map(fn($media) => $media->getUrl());
    }

    /**
     * Get the main image URL (first image)
     */
    public function getMainImageUrl()
    {
        $media = $this->getFirstMedia('images');
        return $media ? $media->getUrl() : null;
    }

    /**
     * Calculate the final price after discount
     */
    public function getFinalPriceAttribute(): float
    {
        return $this->price - $this->discount;
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercentageAttribute(): float
    {
        return $this->price > 0 ? round(($this->discount / $this->price) * 100, 2) : 0;
    }

    /**
     * Check if in stock
     */
    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Check if out of stock
     */
    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }

    /**
     * Check if has discount
     */
    public function hasDiscount(): bool
    {
        return $this->discount > 0;
    }

    /**
     * Check if stock is low
     */
    public function isLowStock(): bool
    {
        return $this->stock <= $this->min_stock_alert;
    }

    /**
     * Get total dimensions (volume in cm³)
     */
    public function getVolumeAttribute(): ?float
    {
        if ($this->length && $this->width && $this->height) {
            return $this->length * $this->width * $this->height;
        }
        return null;
    }

    /**
     * Get weight in grams
     */
    public function getWeightInGramsAttribute(): ?int
    {
        return $this->weight ? (int)($this->weight * 1000) : null;
    }

    // ------------------------------
    // Setters
    // ------------------------------

    /**
     * Set images (array of files)
     */
    public function setImages(array $files): void
    {
        $this->clearMediaCollection('images');

        if ($files) {
        foreach ($files as $file) {
                $this->addMedia($file)->toMediaCollection('images');
            }
        }
    }
}
