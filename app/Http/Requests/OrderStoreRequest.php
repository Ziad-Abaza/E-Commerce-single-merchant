<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
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
            'promo_code_id' => 'nullable|exists:promo_codes,id',
            'notes' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:8192',
            'invoice' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:8192',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,jpg,jpeg,png|max:8192',
            'items' => 'required|array|min:1',
            'items.*.product_detail_id' => 'required|exists:product_details,id',
            'items.*.quantity' => 'required|integer|min:1',
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
            'notes.string' => 'Notes must be a string.',
            'receipt.file' => 'Receipt must be a file.',
            'receipt.mimes' => 'Receipt must be a file of type: pdf, jpg, jpeg, png.',
            'receipt.max' => 'Receipt may not be greater than 8192 kilobytes.',
            'invoice.file' => 'Invoice must be a file.',
            'invoice.mimes' => 'Invoice must be a file of type: pdf, jpg, jpeg, png.',
            'invoice.max' => 'Invoice may not be greater than 8192 kilobytes.',
            'attachments.array' => 'Attachments must be an array.',
            'attachments.*.file' => 'Each attachment must be a file.',
            'attachments.*.mimes' => 'Each attachment must be a file of type: pdf, jpg, jpeg, png.',
            'attachments.*.max' => 'Each attachment may not be greater than 8192 kilobytes.',
            'items.required' => 'At least one order item is required.',
            'items.*.product_detail_id.required' => 'Product detail ID is required for each item.',
            'items.*.product_detail_id.exists' => 'The selected product detail is invalid.',
            'items.*.quantity.required' => 'Quantity is required for each item.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
        ];
    }
}
