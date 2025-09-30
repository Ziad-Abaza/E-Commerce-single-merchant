<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
     * Get the total stock by summing all product details' stock
     *
     * @return int
     */
    public function getTotalStockAttribute(): int
    {
        return (int) $this->details()->sum('stock');
    }

    /**
     * Determine if the product requires opening the product page before adding to cart
     *
     * @return bool
     */
    public function checkPreventDirectAddToCart(): bool
    {
        // Get prevent_direct_add_to_cart setting from sitting table
        $preventDirect = (bool) DB::table('Settings')
            ->where('key', 'prevent_direct_add_to_cart')
            ->value('value');

        // Count product details
        $detailsCount = $this->details()->count();

        // Check if any product detail has attributes that are variants
        $hasVariant = $this->details()->whereHas('attributes', function ($query) {
            $query->where('is_variant', true);
        })->exists();

        return $preventDirect && $detailsCount > 1 && $hasVariant;
    }


    /**
     * Get the categories that belong to this product.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'categories_products');
    }

    /**
     * Get the product details (variants) for this product.
     */
    public function details(): HasMany
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

    /**
     * Generate a unique SKU for the product.
     *
     * @param string $name The product name
     * @param string|null $brand The product brand (optional)
     * @return string The generated SKU
     */
    public static function generateSku(string $name, ?string $brand = null): string
    {
        $prefix = '';
        
        // Use first 3 letters of brand (or PRD if no brand)
        if ($brand) {
            $prefix .= strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $brand), 0, 3));
        }
        
        if (empty($prefix)) {
            $prefix = 'PRD';
        }
        
        // Add first 3 letters of product name
        $prefix .= '-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 3));
        
        // Generate a unique SKU
        do {
            $sku = $prefix . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('sku', $sku)->exists());
        
        return $sku;
    }
}
