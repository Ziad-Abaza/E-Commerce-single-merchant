<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentUpdateRequest extends FormRequest
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
            'order_id' => 'sometimes|required|exists:orders,id',
            'amount' => 'sometimes|required|numeric|min:0',
            'currency' => 'sometimes|required|string|max:3',
            'payment_method' => 'sometimes|required|string|max:50',
            'transaction_id' => 'nullable|string|max:255',
            'gateway_response' => 'nullable|array',
            'status' => 'sometimes|required|string|in:pending,completed,failed,refunded',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:8192',
            'proof_of_payment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:8192',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:8192',
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
            'order_id.required' => 'Order ID is required.',
            'order_id.exists' => 'The selected order does not exist.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a number.',
            'amount.min' => 'Amount must be at least 0.',
            'currency.required' => 'Currency is required.',
            'currency.string' => 'Currency must be a string.',
            'currency.max' => 'Currency may not be greater than 3 characters.',
            'payment_method.required' => 'Payment method is required.',
            'payment_method.string' => 'Payment method must be a string.',
            'payment_method.max' => 'Payment method may not be greater than 50 characters.',
            'transaction_id.string' => 'Transaction ID must be a string.',
            'transaction_id.max' => 'Transaction ID may not be greater than 255 characters.',
            'gateway_response.array' => 'Gateway response must be an array.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be one of: pending, completed, failed, refunded.',
            'receipt.file' => 'Receipt must be a file.',
            'receipt.mimes' => 'Receipt must be a file of type: pdf, jpg, jpeg, png.',
            'receipt.max' => 'Receipt may not be greater than 8192 kilobytes.',
            'proof_of_payment.file' => 'Proof of payment must be a file.',
            'proof_of_payment.mimes' => 'Proof of payment must be a file of type: pdf, jpg, jpeg, png.',
            'proof_of_payment.max' => 'Proof of payment may not be greater than 8192 kilobytes.',
            'document.file' => 'Document must be a file.',
            'document.mimes' => 'Document must be a file of type: pdf, jpg, jpeg, png.',
            'document.max' => 'Document may not be greater than 8192 kilobytes.',
        ];
    }
}
