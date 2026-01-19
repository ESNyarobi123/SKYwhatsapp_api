<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'zenopay_api_key' => ['sometimes', 'nullable', 'string', 'max:255'],
            'zenopay_test_mode' => ['sometimes', 'boolean'],
            'paypal_email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'paypal_me_username' => ['sometimes', 'nullable', 'string', 'max:255'],
            'trc20_wallet_address' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}