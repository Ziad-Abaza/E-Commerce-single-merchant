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
            'user_id' => 'required|exists:users,id',
            'status' => 'required|string|in:pending,confirmed,shipped,delivered,cancelled',
            'total_amount' => 'required|numeric|min:0',
            'shipping_amount' => 'nullable|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:3',
            'shipping_address' => 'required|string',
            'notes' => 'nullable|string',
            'payment_method' => 'required|string|max:50',
            'payment_status' => 'required|string|in:pending,paid,failed,refunded',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:8192',
            'invoice' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:8192',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,jpg,jpeg,png|max:8192',
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
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be one of: pending, confirmed, shipped, delivered, cancelled.',
            'total_amount.required' => 'Total amount is required.',
            'total_amount.numeric' => 'Total amount must be a number.',
            'total_amount.min' => 'Total amount must be at least 0.',
            'shipping_amount.numeric' => 'Shipping amount must be a number.',
            'shipping_amount.min' => 'Shipping amount must be at least 0.',
            'shipping_cost.numeric' => 'Shipping cost must be a number.',
            'shipping_cost.min' => 'Shipping cost must be at least 0.',
            'tax_amount.numeric' => 'Tax amount must be a number.',
            'tax_amount.min' => 'Tax amount must be at least 0.',
            'discount_amount.numeric' => 'Discount amount must be a number.',
            'discount_amount.min' => 'Discount amount must be at least 0.',
            'currency.required' => 'Currency is required.',
            'currency.string' => 'Currency must be a string.',
            'currency.max' => 'Currency may not be greater than 3 characters.',
            'shipping_address.required' => 'Shipping address is required.',
            'shipping_address.string' => 'Shipping address must be a string.',
            'notes.string' => 'Notes must be a string.',
            'payment_method.required' => 'Payment method is required.',
            'payment_method.string' => 'Payment method must be a string.',
            'payment_method.max' => 'Payment method may not be greater than 50 characters.',
            'payment_status.required' => 'Payment status is required.',
            'payment_status.in' => 'Payment status must be one of: pending, paid, failed, refunded.',
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
        ];
    }
}
