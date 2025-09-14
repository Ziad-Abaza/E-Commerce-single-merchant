<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class WishlistCategory extends Model implements HasMedia
{
    /**
     * The "booting" method of the model.
     */
    protected static function booted()
    {
        // Ensure each user has a default 'Favorites' category
        static::saved(function ($model) {
            if (!static::where('user_id', $model->user_id)->default()->exists()) {
                static::create([
                    'user_id' => $model->user_id,
                    'name' => 'Favorites',
                    'is_default' => true,
                ]);
            }
        });

        // Prevent deletion of default 'Favorites' category
        static::deleting(function ($model) {
            if ($model->is_default && $model->name === 'Favorites') {
                return false;
            }
        });
    }
    use HasFactory, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'is_default',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Register media collections for the model.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('icon')
            ->useDisk('public')
            ->singleFile()
            ->withResponsiveImages();

        $this->addMediaCollection('image')
            ->useDisk('public')
            ->singleFile()
            ->withResponsiveImages();
    }

    /**
     * Register media conversions for the model.
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(50)
            ->height(50)
            ->sharpen(10);

        $this->addMediaConversion('small')
            ->width(100)
            ->height(100)
            ->sharpen(10);

        $this->addMediaConversion('medium')
            ->width(200)
            ->height(200)
            ->sharpen(10);
    }

    /**
     * Get the user that owns this wishlist category.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the wishlist items for this category.
     */
    public function items()
    {
        return $this->hasMany(WishlistItem::class);
    }

    /**
     * Get the products in this wishlist category.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'wishlist_items');
    }

    /**
     * Scope a query to only include default wishlist categories.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope a query to only include custom wishlist categories.
     */
    public function scopeCustom($query)
    {
        return $query->where('is_default', false);
    }

    /**
     * Get the category icon.
     */
    public function getIconUrl()
    {
        return $this->getFirstMedia('icon');
    }

    /**
     * Get the category image.
     */
    public function getImageUrl()
    {
        return $this->getFirstMedia('image');
    }

    public function setImage($file)
    {
        $this->clearMediaCollection('image');
        $this->addMedia($file)->toMediaCollection('image');
    }

    public function setIcon($file)
    {
        $this->clearMediaCollection('icon');
        $this->addMedia($file)->toMediaCollection('icon');
    }
    /**
     * Check if this is the default wishlist category.
     */
    public function isDefault()
    {
        return $this->is_default;
    }

    /**
     * Check if this is a custom wishlist category.
     */
    public function isCustom()
    {
        return !$this->is_default;
    }

    /**
     * Get the number of items in this wishlist category.
     */
    public function getItemCountAttribute()
    {
        return $this->items()->count();
    }
}
