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
        $ledgerInvoiceRequiresMobile = $this->filled('credit_ledger_id');

        return [
            'mobile' => [
                ...($ledgerInvoiceRequiresMobile && !$this->filled('mobile_number') ? ['required'] : []),
                'required_without_all:customer_id,postergali_id,postergaliid,mobile_number',
                'nullable',
                'string',
                'max:20',
                'regex:/^\+?[0-9\-\s]{7,15}$/',
            ],
            'mobile_number' => [
                ...($ledgerInvoiceRequiresMobile && !$this->filled('mobile')
                    ? ['required']
                    : ['sometimes']),
                'nullable',
                'string',
                'max:20',
                'regex:/^\+?[0-9\-\s]{7,15}$/',
            ],
            'customer_id' => ['sometimes', 'nullable', 'string', 'max:50'],
            'postergali_id' => ['sometimes', 'nullable', 'string', 'max:50'],
            'postergaliid' => ['sometimes', 'nullable', 'string', 'max:50'],
            'transaction_uuid' => ['sometimes', 'nullable', 'string'],
            'transaction_id' => ['sometimes', 'nullable', 'string'],
            'order_id' => ['sometimes', 'nullable', 'string'],
            'credit_ledger_id' => ['sometimes', 'integer', 'min:1'],
            'download' => ['sometimes', 'nullable', 'boolean'],
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
            'mobile.required_without_all' => 'Mobile number or PosterGali ID is required to generate the invoice.',
            'mobile.regex' => 'Please provide a valid mobile phone number format.',
            'mobile_number.regex' => 'Please provide a valid mobile phone number format.',
        ];
    }

    /**
     * Get sanitized normalized mobile number.
     */
    public function normalizedMobile(): ?string
    {
        $raw = $this->input('mobile') ?? $this->input('mobile_number');
        return $raw ? preg_replace('/[^0-9]/', '', (string) $raw) : null;
    }

    /**
     * Get resolved PosterGali Customer ID.
     */
    public function resolvedCustomerId(): ?string
    {
        return $this->input('customer_id')
            ?? $this->input('postergali_id')
            ?? $this->input('postergaliid');
    }

    /**
     * Get resolved transaction identifier (UUID, Apple txn ID, or Razorpay order ID).
     */
    public function resolvedTransactionId(): ?string
    {
        return $this->input('transaction_uuid')
            ?? $this->input('transaction_id')
            ?? $this->input('order_id');
    }

    /**
     * Check if user explicitly requested forced download attachment instead of direct inline open.
     */
    public function wantsDownload(): bool
    {
        return $this->boolean('download', false);
    }
}
