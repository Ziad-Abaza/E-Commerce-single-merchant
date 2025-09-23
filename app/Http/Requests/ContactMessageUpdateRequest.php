<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactMessageUpdateRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255',
            'subject' => 'sometimes|required|string|max:255',
            'message' => 'sometimes|required|string',
            'phone' => 'nullable|string|max:50',
            'status' => 'nullable|string|in:unread,read,replied,spam',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'subject.required' => 'The subject field is required.',
            'message.required' => 'The message field is required.',
            'status.in' => 'The status must be one of: unread, read, replied, spam.',
        ];
    }
}
