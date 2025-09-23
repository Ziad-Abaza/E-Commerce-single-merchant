<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryStoreRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'thumbnail' => 'nullable|image|max:8192',
            'icon' => 'nullable|image|max:8192',
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
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name may not be greater than 255 characters.',
            'slug.required' => 'Slug is required.',
            'slug.string' => 'Slug must be a string.',
            'slug.max' => 'Slug may not be greater than 255 characters.',
            'slug.unique' => 'The slug has already been taken.',
            'parent_id.exists' => 'The selected parent category does not exist.',
            'description.string' => 'Description must be a string.',
            'is_active.boolean' => 'Active status must be true or false.',
            'sort_order.integer' => 'Sort order must be an integer.',
            'thumbnail.image' => 'Thumbnail must be an image.',
            'thumbnail.max' => 'Thumbnail may not be greater than 8192 kilobytes.',
            'icon.image' => 'Icon must be an image.',
            'icon.max' => 'Icon may not be greater than 8192 kilobytes.',
        ];
    }
}
