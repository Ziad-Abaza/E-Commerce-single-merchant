<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Cart extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'product_detail_id',
        'quantity',
        'promo_code_id',
        'discount_amount',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
    ];
    /**
     * Get the user that owns this cart item.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product detail for this cart item.
     */
    public function productDetail()
    {
        return $this->belongsTo(ProductDetail::class);
    }

    /**
     * Get the product through the product detail.
     */
    public function product()
    {
        return $this->hasOneThrough(Product::class, ProductDetail::class, 'id', 'id', 'product_detail_id', 'product_id');
    }

    /**
     * Calculate the total price for this cart item.
     */
    public function getTotalPriceAttribute()
    {
        return $this->productDetail->final_price * $this->quantity;
    }

    /**
     * Check if the cart item is in stock.
     */
    public function isInStock()
    {
        return $this->productDetail->isInStock() && $this->productDetail->stock >= $this->quantity;
    }

    /**
     * Check if the cart item has a discount.
     */
    public function hasDiscount()
    {
        return $this->productDetail->hasDiscount();
    }

    /**
     * Get the discount amount for this cart item.
     */
    public function getDiscountAmountAttribute()
    {
        return $this->productDetail->discount * $this->quantity;
    }

    /**
     * Get the discount percentage for this cart item.
     */
    public function getDiscountPercentageAttribute()
    {
        return $this->productDetail->discount_percentage;
    }

    public function items()
    {
        return $this->hasMany(Cart::class, 'user_id', 'user_id');
    }
}
