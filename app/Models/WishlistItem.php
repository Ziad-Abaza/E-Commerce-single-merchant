<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WishlistItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'wishlist_category_id',
        'product_id',
    ];

    /**
     * Get the wishlist category that owns this item.
     */
    public function category()
    {
        return $this->belongsTo(WishlistCategory::class, 'wishlist_category_id');
    }

    /**
     * Get the product for this wishlist item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user through the wishlist category.
     */
    public function user()
    {
        return $this->hasOneThrough(User::class, WishlistCategory::class, 'id', 'id', 'wishlist_category_id', 'user_id');
    }

    /**
     * Check if the product is in stock.
     */
    public function isProductInStock()
    {
        return $this->product->details()->where('stock', '>', 0)->exists();
    }

    /**
     * Check if the product has a discount.
     */
    public function hasProductDiscount()
    {
        return $this->product->details()->where('discount', '>', 0)->exists();
    }

    /**
     * Get the lowest price for the product.
     */
    public function getLowestPriceAttribute()
    {
        return $this->product->details()->min('price');
    }

    /**
     * Get the highest price for the product.
     */
    public function getHighestPriceAttribute()
    {
        return $this->product->details()->max('price');
    }

    /**
     * Get the lowest final price for the product.
     */
    public function getLowestFinalPriceAttribute()
    {
        return $this->product->details()->min(DB::raw('price - discount'));
    }

    /**
     * Get the highest final price for the product.
     */
    public function getHighestFinalPriceAttribute()
    {
        return $this->product->details()->max(DB::raw('price - discount'));
    }
}
