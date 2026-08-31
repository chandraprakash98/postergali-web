<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateInvoiceRequest extends FormRequest
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
            'mobile' => [
                'required',
                'string',
                'max:20',
                'regex:/^\+?[0-9\-\s]{7,15}$/',
            ],
        ];
    }

    /**
     * Custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'mobile.required' => 'Mobile number is required to generate the invoice.',
            'mobile.regex' => 'Please provide a valid mobile phone number format.',
        ];
    }

    /**
     * Get sanitized normalized mobile number.
     */
    public function normalizedMobile(): string
    {
        return preg_replace('/[^0-9]/', '', (string) $this->input('mobile', ''));
    }
}
