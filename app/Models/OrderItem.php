<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class OrderItem extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'order_id',
        'product_detail_id',
        'product_name',
        'product_sku',
        'quantity',
        'unit_price',
        'total_price',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * Register media collections for the model.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('document')
            ->useDisk('public')->singleFile();

        $this->addMediaCollection('note')
            ->useDisk('public')
            ->singleFile();

        $this->addMediaCollection('attachment')
            ->useDisk('public')
            ->singleFile();
    }

    /**
     * Register media conversions for the model.
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(100)
            ->height(100)
            ->sharpen(10);

        $this->addMediaConversion('medium')
            ->width(300)
            ->height(300)
            ->sharpen(10);
    }

    /**
     * Get the order that owns this item.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product detail for this order item.
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
     * Get the user through the order.
     */
    public function user()
    {
        return $this->hasOneThrough(User::class, Order::class, 'id', 'id', 'order_id', 'user_id');
    }

    /**
     * Get the item documents.
     */
    public function getDocumentsUrl()
    {
        return $this->getMedia('document');
    }

    /**
     * Get the item notes.
     */
    public function getNotesUrl()
    {
        return $this->getMedia('note');
    }

    /**
     * Get the item attachments.
     */
    public function getAttachmentsUrl()
    {
        return $this->getMedia('attachment');
    }

    public function setDocument($file)
    {
        $this->clearMediaCollection('document');
        $this->addMedia($file)->toMediaCollection('document');
    }

    public function setNote($file)
    {
        $this->clearMediaCollection('note');
        $this->addMedia($file)->toMediaCollection('note');
    }

    public function setAttachment($file)
    {
        $this->clearMediaCollection('attachment');
        $this->addMedia($file)->toMediaCollection('attachment');
    }

    /**
     * Calculate the total price for this item.
     */
    public function calculateTotalPrice()
    {
        return $this->unit_price * $this->quantity;
    }

    /**
     * Check if the total price is correctly calculated.
     */
    public function isTotalPriceCorrect()
    {
        return $this->total_price === $this->calculateTotalPrice();
    }
}
