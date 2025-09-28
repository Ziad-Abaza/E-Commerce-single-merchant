<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ProductDetail;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductAttributeController extends Controller
{
    /**
     * Get all attributes for a product variant.
     *
     * @param  int  $productDetailId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($productDetailId)
    {
        $productDetail = ProductDetail::with(['product.categories', 'attributeValues.attribute'])->findOrFail($productDetailId);
        
        // Get all attributes from the product's categories
        $attributes = collect();
        foreach ($productDetail->product->categories as $category) {
            $attributes = $attributes->merge($category->allAttributes());
        }
        $attributes = $attributes->unique('id');
        
        // Map attributes with their values
        $attributesWithValues = $attributes->map(function ($attribute) use ($productDetail) {
            $attributeValue = $productDetail->attributeValues->firstWhere('attribute_id', $attribute->id);
            
            return [
                'id' => $attribute->id,
                'name' => $attribute->name,
                'slug' => $attribute->slug,
                'type' => $attribute->type,
                'options' => $attribute->options,
                'is_required' => $attribute->pivot ? $attribute->pivot->is_required : false,
                'value' => $attributeValue ? $attributeValue->value : null,
                'value_id' => $attributeValue ? $attributeValue->id : null,
                'is_variant' => $attribute->is_variant ?? false, // Whether this attribute is used for variant selection
            ];
        });
        
        return response()->json([
            'message' => 'Product variant attributes retrieved successfully.',
            'data' => $attributesWithValues,
            'variant_identifier' => $productDetail->getVariantIdentifier(), // Helper method to identify the variant
        ]);
    }

    /**
     * Update or create attribute values for a product variant.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $productDetailId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $productDetailId)
    {
        $productDetail = ProductDetail::findOrFail($productDetailId);
        
        $validated = $request->validate([
            'attributes' => 'required|array',
            'attributes.*.attribute_id' => 'required|exists:attributes,id',
            'attributes.*.value' => 'required',
        ]);
        
        DB::beginTransaction();
        
        try {
            foreach ($validated['attributes'] as $attributeData) {
                $attribute = Attribute::findOrFail($attributeData['attribute_id']);
                $value = $attributeData['value'];
                
                // Handle different attribute types
                $valueType = gettype($value);
                $valueToStore = $value;
                
                if ($valueType === 'array' || $valueType === 'object') {
                    $valueToStore = json_encode($value);
                    $valueType = 'array';
                }
                
                // Update or create the attribute value
                $productDetail->attributeValues()->updateOrCreate(
                    ['attribute_id' => $attribute->id],
                    [
                        'value' => $valueToStore,
                        'value_type' => $valueType,
                    ]
                );
            }
            
            // Update the variant identifier (e.g., "Red / Large")
            $productDetail->updateVariantIdentifier();
            
            DB::commit();
            
            return response()->json([
                'message' => 'Product variant attributes updated successfully.',
                'data' => $productDetail->load('attributeValues.attribute'),
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Failed to update product variant attributes.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove an attribute value from a product variant.
     *
     * @param  int  $productDetailId
     * @param  int  $attributeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($productDetailId, $attributeId)
    {
        $productDetail = ProductDetail::findOrFail($productDetailId);
        $attribute = Attribute::findOrFail($attributeId);
        
        $productDetail->attributeValues()->where('attribute_id', $attribute->id)->delete();
        
        // Update the variant identifier after removing an attribute
        $productDetail->updateVariantIdentifier();
        
        return response()->json([
            'message' => 'Product variant attribute removed successfully.',
        ]);
    }
    
    /**
     * Get attributes for a product's categories.
     *
     * @param  int  $productDetailId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductCategoryAttributes($productDetailId)
    {
        $productDetail = ProductDetail::with('product.categories.attributes')->findOrFail($productDetailId);

        $attributes = collect();

        foreach ($productDetail->product->categories as $category) {
            $attributes = $attributes->merge(
                $category->attributes()->withPivot(['is_required', 'sort_order'])->get()
            );
            // Include parent attributes if needed
            if ($category->parent) {
                $attributes = $attributes->merge(
                    $category->parent->attributes()->withPivot(['is_required', 'sort_order'])->get()
                );
            }
        }

        $attributes = $attributes->unique('id');

        return response()->json([
            'message' => 'Product category attributes retrieved successfully.',
            'data' => $attributes->map(function ($attribute) {
                return [
                    'id' => $attribute->id,
                    'name' => $attribute->name,
                    'slug' => $attribute->slug,
                    'type' => $attribute->type,
                    'options' => $attribute->options,
                    'is_required' => $attribute->pivot->is_required ?? false,
                    'is_variant' => $attribute->is_variant ?? false,
                ];
            }),
        ]);
    }


    /**
     * Get variant attributes (used for creating variant dropdowns)
     * 
     * @param int $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVariantAttributes($productId)
    {
        $product = Product::with('categories.attributes')->findOrFail($productId);
        
        $attributes = collect();
        
        foreach ($product->categories as $category) {
            $attributes = $attributes->merge($category->allAttributes()
                ->where('is_variant', true));
        }
        
        $attributes = $attributes->unique('id');
        
        return response()->json([
            'message' => 'Variant attributes retrieved successfully.',
            'data' => $attributes->map(function ($attribute) {
                return [
                    'id' => $attribute->id,
                    'name' => $attribute->name,
                    'slug' => $attribute->slug,
                    'type' => $attribute->type,
                    'options' => $attribute->options,
                ];
            }),
        ]);
    }
}
