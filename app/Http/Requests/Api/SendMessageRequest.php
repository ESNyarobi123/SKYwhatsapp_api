<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
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
            'instance_id' => ['required', 'exists:instances,id'],
            'to' => ['required', 'string', 'max:20'],
            'body' => ['nullable', 'string', 'max:4096'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:10240'], // 10MB max
            'metadata' => ['nullable', 'array'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'instance_id.required' => 'Instance ID is required.',
            'instance_id.exists' => 'The specified instance does not exist.',
            'to.required' => 'Recipient phone number is required.',
            'body.required' => 'Message body is required.',
            'body.max' => 'Message body cannot exceed 4096 characters.',
        ];
    }
}
