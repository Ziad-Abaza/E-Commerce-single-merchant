<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Payment extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'order_id',
        'amount',
        'currency',
        'payment_method',
        'transaction_id',
        'gateway_response',
        'status',
        'paid_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'gateway_response' => 'array',
    ];

    /**
     * Register media collections for the model.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('receipts')
            ->useDisk('public')
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png'])
            ->singleFile();

        $this->addMediaCollection('proof_of_payment')
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'application/pdf'])
            ->singleFile();

        $this->addMediaCollection('document')
            ->useDisk('public')->singleFile();
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
     * Get the order that owns this payment.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user through the order.
     */
    public function user()
    {
        return $this->hasOneThrough(User::class, Order::class, 'id', 'id', 'order_id', 'user_id');
    }

    /**
     * Scope a query to only include pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include completed payments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include failed payments.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope a query to only include refunded payments.
     */
    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    /**
     * Get the payment receipt.
     */
    public function getReceiptUrl()
    {
        return $this->getFirstMedia('receipts');
    }

    /**
     * Get the proof of payment.
     */
    public function getProofOfPaymentUrl()
    {
        return $this->getFirstMedia('proof_of_payment');
    }

    /**
     * Get the payment documents.
     */
    public function getDocumentUrl()
    {
        return $this->getMedia('document');
    }

    public function setReceipt($file)
    {
        $this->clearMediaCollection('receipts');
        $this->addMedia($file)->toMediaCollection('receipts');
    }

    public function setProofOfPayment($file)
    {
        $this->clearMediaCollection('proof_of_payment');
        $this->addMedia($file)->toMediaCollection('proof_of_payment');
    }

    public function setDocument($file)
    {
        $this->clearMediaCollection('document');
        $this->addMedia($file)->toMediaCollection('document');
    }

    /**
     * Check if the payment is completed.
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the payment is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the payment is failed.
     */
    public function isFailed()
    {
        return $this->status === 'failed';
    }

    /**
     * Check if the payment is refunded.
     */
    public function isRefunded()
    {
        return $this->status === 'refunded';
    }

    /**
     * Mark the payment as completed.
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark the payment as failed.
     */
    public function markAsFailed()
    {
        $this->update([
            'status' => 'failed',
        ]);
    }

    /**
     * Mark the payment as refunded.
     */
    public function markAsRefunded()
    {
        $this->update([
            'status' => 'refunded',
        ]);
    }

    /**
     * Get the formatted amount with currency.
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }

    /**
     * Check if the payment has a transaction ID.
     */
    public function hasTransactionId()
    {
        return !empty($this->transaction_id);
    }

    /**
     * Check if the payment has gateway response.
     */
    public function hasGatewayResponse()
    {
        return !empty($this->gateway_response);
    }
}
