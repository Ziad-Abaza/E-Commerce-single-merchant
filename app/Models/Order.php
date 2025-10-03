<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Order extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'promo_code_id',
        'order_number',
        'status',
        'total_amount',
        'shipping_amount',
        'shipping_cost',
        'tax_amount',
        'discount_amount',
        'currency',
        'shipping_address',
        'notes',
        'delivered_at',
        'cancelled_at',
        'phone',
    ];

    /**
     * Get the promo code associated with the order.
     */
    public function promoCode()
    {
        return $this->belongsTo(PromoCode::class);
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Register media collections for the model.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('receipt')
            ->useDisk('public')
            ->singleFile();

        $this->addMediaCollection('invoice')
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
            ->width(150)
            ->height(150)
            ->sharpen(10);

        $this->addMediaConversion('medium')
            ->width(400)
            ->height(400)
            ->sharpen(10);
    }

    /**
     * Get the user that owns this order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items for this order.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scope a query to only include pending orders.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include confirmed orders.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope a query to only include shipped orders.
     */
    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    /**
     * Scope a query to only include delivered orders.
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    /**
     * Scope a query to only include cancelled orders.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Get the order receipt.
     */
    public function getReceiptUrl()
    {
        return $this->getFirstMedia('receipt');
    }

    /**
     * Get the order invoice.
     */
    public function getInvoiceUrl()
    {
        return $this->getFirstMedia('invoice');
    }

    /**
     * Get the order attachments.
     */
    public function getAttachmentUrl()
    {
        return $this->getMedia('attachment');
    }

    public function setReceipt($file)
    {
        $this->clearMediaCollection('receipt');
        $this->addMedia($file)->toMediaCollection('receipt');
    }

    public function setInvoice($file)
    {
        $this->clearMediaCollection('invoice');
        $this->addMedia($file)->toMediaCollection('invoice');
    }

    public function setAttachment($files)
    {
        $this->clearMediaCollection('attachment');

        foreach ($files as $file) {
            $this->addMedia($file)->toMediaCollection('attachment');
        }
    }

    /**
     * Calculate the subtotal (total before shipping, tax, and discounts).
     */
    public function getSubtotal()
    {
        return round($this->items->sum(function ($item) {
            return $item->unit_price * $item->quantity;
        }), 2);
    }

    /**
     * Calculate the final total.
     */
    public function getFinalTotal()
    {
        return $this->total_amount + $this->shipping_cost + $this->tax_amount - $this->discount_amount;
    }

    /**
     * Check if the order is delivered.
     */
    public function isDelivered()
    {
        return $this->status === 'delivered';
    }

    /**
     * Check if the order is cancelled.
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if the order can be cancelled.
     */
    public function canBeCancelled()
    {
        return !in_array($this->status, ['delivered', 'cancelled', 'refunded']);
    }

    /**
     * Mark the order as delivered.
     */
    public function markAsDelivered()
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    /**
     * Mark the order as cancelled.
     */
    public function markAsCancelled()
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }
}
