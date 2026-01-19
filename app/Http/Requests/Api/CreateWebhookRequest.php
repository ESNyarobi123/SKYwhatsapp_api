<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateWebhookRequest extends FormRequest
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
            'url' => ['required', 'url', 'max:512'],
            'events' => ['required', 'array', 'min:1'],
            'events.*' => ['required', 'string', 'in:message.inbound,message.status,instance.connected,instance.disconnected,billing.expiring'],
            'instance_id' => ['nullable', 'exists:instances,id'],
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
            'url.required' => 'Webhook URL is required.',
            'url.url' => 'Webhook URL must be a valid URL.',
            'events.required' => 'At least one event type is required.',
            'events.array' => 'Events must be an array.',
            'events.*.in' => 'Invalid event type. Allowed: message.inbound, message.status, instance.connected, instance.disconnected, billing.expiring.',
        ];
    }
}
