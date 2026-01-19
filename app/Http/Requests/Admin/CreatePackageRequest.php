<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreatePackageRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', 'unique:packages,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3', 'default:USD'],
            'duration_days' => ['required', 'integer', 'min:1'],
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
