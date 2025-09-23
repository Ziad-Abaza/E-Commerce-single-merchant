<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Category extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'description',
        'is_active',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

      /**
     * The "booted" method of the model.
     * Automatically handles slug generation.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($category) {
            $category->slug = Str::slug($category->name);
        });

        static::updating(function ($category) {
            if ($category->isDirty('name')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Register media collections for the model.
     */
    
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/jpg'])
            ->withResponsiveImages();

        $this->addMediaCollection('icons')
            ->useDisk('public')
            ->acceptsMimeTypes(['image/svg+xml', 'image/png', 'image/webp', 'image/jpeg', 'image/jpg'])
            ->singleFile();
    }

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

        $this->addMediaConversion('large')
            ->width(600)
            ->height(600)
            ->sharpen(10);
    }

    /**
     * Get the parent category.
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get all descendants of this category.
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get all ancestors of this category.
     */
    public function ancestors()
    {
        return $this->parent()->with('ancestors');
    }

    /**
     * Get the products that belong to this category.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'categories_products');
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive categories.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to only include root categories (no parent).
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get the category image.
     */
    public function getThumbnailUrl()
    {
        $media = $this->getFirstMedia('thumbnail');
        return $media ? $media->getUrl() : null;
    }

    /**
     * Get the category icon.
     */
    public function getIconUrl()
    {
        $media = $this->getFirstMedia('icons');
        return $media ? $media->getUrl() : null;
    }

    public function setThumbnail($file): void
    {
        $this->clearMediaCollection('thumbnail');
        if ($file) {
            $this->addMedia($file)->toMediaCollection('thumbnail');
        }
    }

    public function setIcon($file): void
    {
        $this->clearMediaCollection('icons');
        if ($file) {
        $this->addMedia($file)->toMediaCollection('icons');
        }
    }

    /**
     * Check if category has children.
     */
    public function hasChildren()
    {
        return $this->children()->exists();
    }

    /**
     * Check if category is a root category.
     */
    public function isRoot()
    {
        return is_null($this->parent_id);
    }

    /**
     * Get the full path of the category.
     */
    public function getFullPathAttribute()
    {
        $path = collect([$this]);
        $parent = $this->parent;

        while ($parent) {
            $path->prepend($parent);
            $parent = $parent->parent;
        }

        return $path->pluck('name')->implode(' > ');
    }
}
