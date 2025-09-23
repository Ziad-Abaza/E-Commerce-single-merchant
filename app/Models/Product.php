<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'brand',
        'short_description',
        'description',
        'sku',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($product) {
            $product->slug = Str::slug($product->name);
        });

        static::updating(function ($product) {
            if ($product->isDirty('name')) {
                $product->slug = Str::slug($product->name);
            }
        });
    }
    /**
     * Get the categories that belong to this product.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'categories_products');
    }

    /**
     * Get the product details for this product.
     */
    public function details()
    {
        return $this->hasMany(ProductDetail::class);
    }

    /**
     * Get the reviews for this product.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the order items for this product.
     */
    public function orderItems()
    {
        return $this->hasManyThrough(
            OrderItem::class,
            ProductDetail::class,
            'product_id',
            'product_detail_id',
        );
    }


    /**
     * Get the cart items for this product.
     */
    public function cartItems()
    {
        return $this->hasManyThrough(Cart::class, ProductDetail::class, 'product_id', 'product_detail_id');
    }

    /**
     * Get the wishlist items for this product.
     */
    public function wishlistItems()
    {
        return $this->hasMany(WishlistItem::class);
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive products.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Get the main product image.
     */
    public function getMainImageUrl()
    {
        $detail = $this->details->first();
        $media = $detail ? $detail->getFirstMedia('images') : null;
        return $media ? $media->getUrl() : null;
    }

    /**
     * Get the product gallery images.
     */
    public function getImagesUrl()
    {
        $detail = $this->details->first();
        $media = $detail ? $detail->getMedia('images') : null;
        return $media ? $media->map->getUrl()->toArray() : [];
    }
}
