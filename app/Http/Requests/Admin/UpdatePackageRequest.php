<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePackageRequest extends FormRequest
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
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('packages', 'name')->ignore($this->route('package'))],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'duration_days' => ['sometimes', 'required', 'integer', 'min:1'],
            'features' => ['nullable', 'array'],
            'features.*' => ['string'],
            'feature_instances_limit' => ['nullable', 'integer', 'min:-1'],
            'feature_instances_period' => ['nullable', 'string', 'in:lifetime'],
            'feature_messages_limit' => ['nullable', 'integer', 'min:-1'],
            'feature_messages_period' => ['nullable', 'string', 'in:day,month,year,lifetime'],
            'feature_api_calls_limit' => ['nullable', 'integer', 'min:-1'],
            'feature_api_calls_period' => ['nullable', 'string', 'in:day,month,year,lifetime'],
            'feature_api_keys_limit' => ['nullable', 'integer', 'min:-1'],
            'feature_api_keys_period' => ['nullable', 'string', 'in:lifetime'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
