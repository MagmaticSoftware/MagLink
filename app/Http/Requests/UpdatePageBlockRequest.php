<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePageBlockRequest extends FormRequest
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
            'tenant_id' => 'nullable|string|max:255',
            'slug' => 'required|string|unique:pages,slug,' . $this->pageBlock->id,
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'style' => 'nullable|array',
            'settings' => 'nullable|array',
            'is_active' => 'boolean',
            'views' => 'integer|min:0',
            'last_viewed_at' => 'nullable|date',
            'published_at' => 'nullable|date',
        ];
    }
}
