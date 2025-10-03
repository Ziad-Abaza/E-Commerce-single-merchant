<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PromoCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'target_type',
        'total_usage_limit',
        'per_user_usage_limit',
        'total_usage_count',
        'start_date',
        'end_date',
        'is_active',
    ];

    /**
     * Get the orders associated with the promo code.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    protected $casts = [
        'description' => 'string',
        'discount_value' => 'decimal:2',
        'total_usage_count' => 'integer',
        'total_usage_limit' => 'integer',
        'per_user_usage_limit' => 'integer',
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'promo_code_products');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'promo_code_categories');
    }

    public function usages()
    {
        return $this->hasMany(PromoCodeUsage::class);
    }

    public function isValid(?User $user = null): bool
    {
        if (!$this->is_active) return false;
        if ($this->start_date && now()->lt($this->start_date)) return false;
        if ($this->end_date && now()->gt($this->end_date)) return false;
        if ($this->total_usage_limit && $this->total_usage_count >= $this->total_usage_limit) return false;

        if ($user && $this->per_user_usage_limit) {
            $usage = $this->usages()->where('user_id', $user->id)->first();
            if ($usage && $usage->usage_count >= $this->per_user_usage_limit) {
                return false;
            }
        }

        return true;
    }

    public function applyDiscount(float $amount, float $shipping = 0): float
    {
        switch ($this->target_type) {
            case 'shipping':
                $targetAmount = $shipping;
                break;
            case 'order':
                $targetAmount = $amount + $shipping;
                break;
            default:
                $targetAmount = $amount;
                break;
        }

        if ($this->discount_type === 'percentage') {
            $discount = ($targetAmount * ($this->discount_value / 100));
        } else {
            $discount = $this->discount_value;
        }

        return max(0, $targetAmount - $discount);
    }

    public function incrementUsage(?User $user = null): void
    {
        $this->increment('total_usage_count');

        if ($user) {
            // First try to update existing record with both promo_code_id and user_id
            $updated = $this->usages()
                ->where('user_id', $user->id)
                ->increment('usage_count');

            // If no rows were updated, it means the record doesn't exist, so create it
            if ($updated === 0) {
                $this->usages()->create([
                    'user_id' => $user->id,
                    'usage_count' => 1,
                ]);
            }
        }
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            });
    }
}
