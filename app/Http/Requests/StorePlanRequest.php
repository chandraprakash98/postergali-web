<?php

namespace App\Http\Requests;

use App\Models\Plan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Must be an authenticated administrator
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
            'plan_title' => ['required', 'string', 'max:100'],
            'plan_code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9_\-]+$/',
                'unique:plans,plan_code',
            ],
            'product_id' => [
                'required',
                'string',
                'max:150',
                'regex:/^[a-zA-Z0-9._\-]+$/',
                'unique:plans,product_id',
            ],
            'credits' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'currency' => ['required', 'string', 'size:3', 'regex:/^[A-Z]{3}$/'],
            'plan_type' => ['required', 'string', Rule::in(Plan::SUPPORTED_TYPES)],
            'duration' => ['nullable', 'string', 'max:30'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'plan_code.regex' => 'The plan code format must contain only alphanumeric characters, dashes, and underscores.',
            'product_id.regex' => 'The product id must be a valid reverse-DNS or alphanumeric identifier (e.g. com.postergali.credits.100).',
            'currency.regex' => 'Currency must be a 3-letter uppercase ISO currency code (e.g. INR).',
        ];
    }
}
