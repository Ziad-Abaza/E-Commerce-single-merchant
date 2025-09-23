<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductDetailUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productDetailId = $this->route('detail');

        if ($productDetailId instanceof \App\Models\ProductDetail) {
            $productDetailId = $productDetailId->id;
        }


        return [
            'size' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'material' => 'nullable|string|max:100',
            'weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'origin' => 'nullable|string|max:100',
            'quality' => 'nullable|string|max:50',
            'packaging' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock_alert' => 'nullable|integer|min:0',
            'sku_variant' => 'nullable|string|max:100|unique:product_details,sku_variant,' . $productDetailId,
            'barcode' => 'nullable|string|max:100|unique:product_details,barcode,' . $productDetailId,
            'is_active' => 'boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp,gif|max:8192',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'size.string' => 'Size must be a string.',
            'size.max' => 'Size may not be greater than 50 characters.',
            'color.string' => 'Color must be a string.',
            'color.max' => 'Color may not be greater than 50 characters.',
            'material.string' => 'Material must be a string.',
            'material.max' => 'Material may not be greater than 100 characters.',
            'weight.numeric' => 'Weight must be a number.',
            'weight.min' => 'Weight must be at least 0.',
            'length.numeric' => 'Length must be a number.',
            'length.min' => 'Length must be at least 0.',
            'width.numeric' => 'Width must be a number.',
            'width.min' => 'Width must be at least 0.',
            'height.numeric' => 'Height must be a number.',
            'height.min' => 'Height must be at least 0.',
            'origin.string' => 'Origin must be a string.',
            'origin.max' => 'Origin may not be greater than 100 characters.',
            'quality.string' => 'Quality must be a string.',
            'quality.max' => 'Quality may not be greater than 50 characters.',
            'packaging.string' => 'Packaging must be a string.',
            'packaging.max' => 'Packaging may not be greater than 100 characters.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a number.',
            'price.min' => 'Price must be at least 0.',
            'discount.numeric' => 'Discount must be a number.',
            'discount.min' => 'Discount must be at least 0.',
            'stock.required' => 'Stock is required.',
            'stock.integer' => 'Stock must be an integer.',
            'stock.min' => 'Stock must be at least 0.',
            'min_stock_alert.integer' => 'Minimum stock alert must be an integer.',
            'min_stock_alert.min' => 'Minimum stock alert must be at least 0.',
            'sku_variant.string' => 'SKU variant must be a string.',
            'sku_variant.max' => 'SKU variant may not be greater than 100 characters.',
            'sku_variant.unique' => 'The SKU variant has already been taken.',
            'barcode.string' => 'Barcode must be a string.',
            'barcode.max' => 'Barcode may not be greater than 100 characters.',
            'barcode.unique' => 'The barcode has already been taken.',
            'is_active.boolean' => 'Active status must be true or false.',
            'images.array' => 'Images must be an array.',
            'images.*.image' => 'Each image must be an image file.',
            'images.*.mimes' => 'Each image must be a file of type: jpeg, png, jpg, webp, gif.',
            'images.*.max' => 'Each image may not be greater than 8192 kilobytes.',
        ];
    }
}
