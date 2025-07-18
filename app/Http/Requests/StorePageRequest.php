<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePageRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'company_id' => 'required|exists:companies,id',
            'tenant_id' => 'nullable|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'style' => 'nullable|array',
            'settings' => 'nullable|array',
            'is_active' => 'boolean',
            'published_at' => 'nullable|date',
        ];
    }
}
