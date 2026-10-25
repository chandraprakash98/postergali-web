<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        return $user !== null && (bool) ($user->is_admin ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required_without:plan_title', 'nullable', 'string', 'max:100'],
            'plan_title' => ['required_without:name', 'nullable', 'string', 'max:100'],
            'pack_code' => [
                'required_without:plan_code',
                'nullable',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9_\-]+$/',
                'unique:credit_packs,pack_code',
            ],
            'plan_code' => [
                'required_without:pack_code',
                'nullable',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9_\-]+$/',
                'unique:credit_packs,pack_code',
            ],
            'product_id' => [
                'required',
                'string',
                'max:150',
                'regex:/^[a-zA-Z0-9._\-]+$/',
                'unique:credit_packs,product_id',
            ],
            'credits' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'currency' => ['sometimes', 'string', 'size:3', 'regex:/^[A-Z]{3}$/'],
            'plan_type' => ['sometimes', 'nullable', 'string'],
            'duration' => ['nullable', 'string', 'max:30'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'pack_code.regex' => 'The package code format must contain only alphanumeric characters, dashes, and underscores.',
            'plan_code.regex' => 'The plan code format must contain only alphanumeric characters, dashes, and underscores.',
            'product_id.regex' => 'The product id must be a valid reverse-DNS or alphanumeric identifier (e.g. com.postergali.credits.100).',
            'currency.regex' => 'Currency must be a 3-letter uppercase ISO currency code (e.g. INR).',
        ];
    }

    public function getCreditPackData(): array
    {
        $data = $this->validated();
        return [
            'name' => $data['name'] ?? $data['plan_title'] ?? '',
            'pack_code' => $data['pack_code'] ?? $data['plan_code'] ?? '',
            'product_id' => $data['product_id'],
            'credits' => (int) $data['credits'],
            'price' => $data['price'],
            'currency' => $data['currency'] ?? 'INR',
            'is_active' => (bool) ($data['is_active'] ?? true),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];
    }
}
