<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WishlistCategoryStoreRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'is_default' => 'boolean',
            'icon' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:8192',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:8192',
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
            'user_id.required' => 'User ID is required.',
            'user_id.exists' => 'The selected user does not exist.',
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name may not be greater than 255 characters.',
            'is_default.boolean' => 'Default status must be true or false.',
            'icon.image' => 'Icon must be an image.',
            'icon.mimes' => 'Icon must be a file of type: jpg, jpeg, png, webp, gif.',
            'icon.max' => 'Icon may not be greater than 8192 kilobytes.',
            'image.image' => 'Image must be an image.',
            'image.mimes' => 'Image must be a file of type: jpg, jpeg, png, webp, gif.',
            'image.max' => 'Image may not be greater than 8192 kilobytes.',
        ];
    }
}
