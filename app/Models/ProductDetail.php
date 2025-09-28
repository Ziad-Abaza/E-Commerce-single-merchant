<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'product_id',
        'color',
        'price',
        'discount',
        'stock',
        'min_stock_alert',
        'sku_variant',
        'variant_identifier',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'product_id' => 'integer',
        'color' => 'string',
        'variant_identifier' => 'string',
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'stock' => 'integer',
        'min_stock_alert' => 'integer',
        'sku_variant' => 'string',
        'is_active' => 'boolean',
    ];

    // ------------------------------
    // Media Collections
    // ------------------------------

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useDisk('public')
            ->withResponsiveImages();
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(100)
            ->height(100)
            ->sharpen(10)
            ->nonQueued();

        $this->addMediaConversion('medium')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->nonQueued();

        $this->addMediaConversion('large')
            ->width(600)
            ->height(600)
            ->sharpen(10)
            ->nonQueued();
    }

    /**
     * Generate a unique SKU variant for the product detail.
     *
     * @param string $productSku The base product SKU
     * @param string|null $size The product size (optional)
     * @param string|null $color The product color (optional)
     * @return string The generated SKU variant
     */
    public static function generateSkuVariant(string $productSku, ?string $size = null, ?string $color = null): string
    {
        $baseSku = $productSku;
        $variant = '';
        
        // Add size and color to variant if they exist
        if ($size) {
            $variant .= '-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $size), 0, 3));
        }
        
        if ($color) {
            $variant .= '-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $color), 0, 3));
        }
        
        // If no size or color, add a random 2-digit number
        if (empty($variant)) {
            $variant = '-' . str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT);
        }
        
        // Generate a unique SKU variant
        $counter = 1;
        $skuVariant = $baseSku . $variant;
        $originalSku = $skuVariant;
        
        while (self::where('sku_variant', $skuVariant)->exists()) {
            $skuVariant = $originalSku . '-' . str_pad($counter, 2, '0', STR_PAD_LEFT);
            $counter++;
        }
        
        return $skuVariant;
    }

    // ------------------------------
    // Relations
    // ------------------------------

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    /**
     * Get the attribute values for the product detail.
     */
    public function attributeValues(): HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }
    
    /**
     * Get the attributes for the product detail.
     */
    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'attribute_values')
            ->using(AttributeValue::class)
            ->withPivot(['value', 'value_type']);
    }
    
    /**
     * Get a specific product attribute value by slug.
     *
     * @param string $attributeSlug
     * @return mixed
     */
    public function getProductAttributeValue(string $attributeSlug)
    {
        $attribute = $this->attributeValues()
            ->whereHas('attribute', function($query) use ($attributeSlug) {
                $query->where('slug', $attributeSlug);
            })
            ->first();

        return $attribute ? $attribute->value : null;
    }
    
    /**
     * Set an attribute value for the product detail.
     *
     * @param string $attributeSlug
     * @param mixed $value
     * @return void
     */
    public function setAttributeValue(string $attributeSlug, $value)
    {
        $attribute = Attribute::where('slug', $attributeSlug)->firstOrFail();
        
        $valueType = gettype($value);
        if ($valueType === 'array' || $valueType === 'object') {
            $value = json_encode($value);
            $valueType = 'array';
        }

        $this->attributeValues()->updateOrCreate(
            ['attribute_id' => $attribute->id],
            [
                'value' => $value,
                'value_type' => $valueType,
            ]
        );
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(Cart::class, 'product_detail_id');
    }
    
    /**
     * Get the variant identifier (e.g., "Red / Large")
     * 
     * @return string
     */
    public function getVariantIdentifierAttribute()
    {
        if (!empty($this->attributes['variant_identifier'])) {
            return $this->attributes['variant_identifier'];
        }
        
        // If no variant_identifier is set, generate one from the variant attributes
        return $this->generateVariantIdentifier();
    }
    
    /**
     * Generate a variant identifier based on the variant attributes
     * 
     * @return string
     */
    public function generateVariantIdentifier()
    {
        $identifiers = [];
        
        // Get variant attributes (those marked as is_variant = true)
        $variantAttributes = $this->attributes()
            ->where('is_variant', true)
            ->orderBy('name')
            ->get();
            
        foreach ($variantAttributes as $attribute) {
            $value = $this->getAttributeValue($attribute->slug);
            if (!empty($value)) {
                if (is_array($value)) {
                    $value = implode(', ', $value);
                }
                $identifiers[] = $value;
            }
        }
        
        // Fallback to color if no variant attributes are set
        if (empty($identifiers) && !empty($this->color)) {
            $identifiers[] = $this->color;
        }
        
        return implode(' / ', $identifiers);
    }
    
    /**
     * Update the variant identifier based on current attributes
     * 
     * @return bool
     */
    public function updateVariantIdentifier()
    {
        $this->variant_identifier = $this->generateVariantIdentifier();
        return $this->save();
    }
    
    /**
     * Get the display name for the variant (product name + variant identifier)
     * 
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        $name = $this->product->name;
        $variant = $this->variant_identifier;
        
        if (!empty($variant)) {
            return "{$name} - {$variant}";
        }
        
        return $name;
    }

    // ------------------------------
    // Scopes
    // ------------------------------

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'min_stock_alert');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stock', '<=', 0);
    }

    // ------------------------------
    // Attributes & Getters
    // ------------------------------

    /**
     * Get all images URLs
     */
    public function getImagesUrl()
    {
        return $this->getMedia('images')->map(fn($media) => $media->getUrl());
    }

    /**
     * Get the main image URL (first image)
     */
    public function getMainImageUrl()
    {
        $media = $this->getFirstMedia('images');
        return $media ? $media->getUrl() : null;
    }

    /**
     * Calculate the final price after discount
     */
    public function getFinalPriceAttribute(): float
    {
        return $this->price - $this->discount;
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercentageAttribute(): float
    {
        return $this->price > 0 ? round(($this->discount / $this->price) * 100, 2) : 0;
    }

    /**
     * Check if in stock
     */
    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Check if out of stock
     */
    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }

    /**
     * Check if has discount
     */
    public function hasDiscount(): bool
    {
        return $this->discount > 0;
    }

    /**
     * Check if stock is low
     */
    public function isLowStock(): bool
    {
        return $this->stock <= $this->min_stock_alert;
    }

    /**
     * Get total dimensions (volume in cm³)
     */
    public function getVolumeAttribute(): ?float
    {
        if ($this->length && $this->width && $this->height) {
            return $this->length * $this->width * $this->height;
        }
        return null;
    }

    /**
     * Get weight in grams
     */
    public function getWeightInGramsAttribute(): ?int
    {
        return $this->weight ? (int)($this->weight * 1000) : null;
    }

    // ------------------------------
    // Setters
    // ------------------------------

    /**
     * Set images (array of files)
     */
    public function setImages(array $files): void
    {
        $this->clearMediaCollection('images');

        if ($files) {
        foreach ($files as $file) {
                $this->addMedia($file)->toMediaCollection('images');
            }
        }
    }
}
