<?php

namespace App\Http\Requests;

use App\Models\Plan;
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
        $plan = $this->route('plan') ?? $this->route('package') ?? $this->route('id');
        $planId = is_object($plan) ? $plan->id : $plan;

        return [
            'plan_title' => ['sometimes', 'required', 'string', 'max:100'],
            'plan_code' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9_\-]+$/',
                Rule::unique('plans', 'plan_code')->ignore($planId),
            ],
            'product_id' => [
                'sometimes',
                'required',
                'string',
                'max:150',
                'regex:/^[a-zA-Z0-9._\-]+$/',
                Rule::unique('plans', 'product_id')->ignore($planId),
            ],
            'credits' => ['sometimes', 'required', 'integer', 'min:1'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0', 'max:99999999.99'],
            'currency' => ['sometimes', 'required', 'string', 'size:3', 'regex:/^[A-Z]{3}$/'],
            'plan_type' => ['sometimes', 'required', 'string', Rule::in(Plan::SUPPORTED_TYPES)],
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
