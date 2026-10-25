<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePlanRequest extends FormRequest
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
        $pack = $this->route('plan') ?? $this->route('package') ?? $this->route('id');
        $packId = is_object($pack) ? $pack->id : $pack;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'plan_title' => ['sometimes', 'required', 'string', 'max:100'],
            'pack_code' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9_\-]+$/',
                Rule::unique('credit_packs', 'pack_code')->ignore($packId),
            ],
            'plan_code' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9_\-]+$/',
                Rule::unique('credit_packs', 'pack_code')->ignore($packId),
            ],
            'product_id' => [
                'sometimes',
                'required',
                'string',
                'max:150',
                'regex:/^[a-zA-Z0-9._\-]+$/',
                Rule::unique('credit_packs', 'product_id')->ignore($packId),
            ],
            'credits' => ['sometimes', 'required', 'integer', 'min:1'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0', 'max:99999999.99'],
            'currency' => ['sometimes', 'required', 'string', 'size:3', 'regex:/^[A-Z]{3}$/'],
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
        $clean = [];

        if (isset($data['name']) || isset($data['plan_title'])) {
            $clean['name'] = $data['name'] ?? $data['plan_title'];
        }
        if (isset($data['pack_code']) || isset($data['plan_code'])) {
            $clean['pack_code'] = $data['pack_code'] ?? $data['plan_code'];
        }
        if (isset($data['product_id'])) {
            $clean['product_id'] = $data['product_id'];
        }
        if (isset($data['credits'])) {
            $clean['credits'] = (int) $data['credits'];
        }
        if (isset($data['price'])) {
            $clean['price'] = $data['price'];
        }
        if (isset($data['currency'])) {
            $clean['currency'] = $data['currency'];
        }
        if (isset($data['is_active'])) {
            $clean['is_active'] = (bool) $data['is_active'];
        }
        if (isset($data['sort_order'])) {
            $clean['sort_order'] = (int) $data['sort_order'];
        }

        return $clean;
    }
}
