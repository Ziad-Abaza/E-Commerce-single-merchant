<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttributeUpdateRequest extends FormRequest
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
        $attributeId = $this->route('attribute');

        return [
            'name' => 'sometimes|required|string|max:255',
            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('attributes')->ignore($attributeId),
            ],
            'type' => 'sometimes|required|string|in:text,number,select,checkbox,radio,textarea',
            'options' => 'nullable|array',
            'is_required' => 'sometimes|boolean',
            'is_filterable' => 'sometimes|boolean',
            'is_variant' => 'sometimes|boolean',
            'is_visible_on_frontend' => 'sometimes|boolean',
            'sort_order' => 'nullable|integer',
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
            'slug.required' => 'Slug is required.',
            'slug.unique' => 'The slug has already been taken.',
            'type.required' => 'Type is required.',
            'type.in' => 'Invalid attribute type selected.',
        ];
    }
}

