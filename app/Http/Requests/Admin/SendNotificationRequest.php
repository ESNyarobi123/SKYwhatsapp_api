<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SendNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
            'type' => ['required', 'string', 'in:admin_message,info,warning,success'],
            'priority' => ['sometimes', 'string', 'in:low,normal,high,urgent'],
            'icon' => ['nullable', 'string', 'max:10'],
            'action_url' => ['nullable', 'string', 'url', 'max:500'],
            'action_text' => ['nullable', 'string', 'max:50'],
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
            'title.required' => 'Title is required.',
            'message.required' => 'Message is required.',
            'type.required' => 'Type is required.',
            'type.in' => 'Invalid notification type.',
        ];
    }
}
