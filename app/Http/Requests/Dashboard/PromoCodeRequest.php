<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PromoCodeRequest extends FormRequest
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
        $promoCode = $this->route('promoCode');
        $rules = [
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('promo_codes', 'code')->ignore($promoCode?->id)
            ],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'discount_type' => ['sometimes', 'required', 'in:percentage,fixed'],
            'discount_value' => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'target_type' => ['sometimes', 'required', 'in:products,categories,shipping,order'],
            'total_usage_limit' => ['nullable', 'integer', 'min:1'],
            'per_user_usage_limit' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['nullable', 'date', 'after_or_equal:today'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'is_active' => ['sometimes', 'boolean'],
        ];

        if ($this->input('target_type') === 'products') {
            $rules['product_ids'] = ['sometimes', 'required', 'array', 'min:1'];
            $rules['product_ids.*'] = ['exists:products,id'];
        }

        if ($this->input('target_type') === 'categories') {
            $rules['category_ids'] = ['sometimes', 'required', 'array', 'min:1'];
            $rules['category_ids.*'] = ['exists:categories,id'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'product_ids.required' => 'Please select at least one product.',
            'category_ids.required' => 'Please select at least one category.',
            'end_date.after' => 'The end date must be after the start date.',
        ];
    }
}
