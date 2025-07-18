<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePageBlockRequest extends FormRequest
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
            'page_id' => 'required|exists:pages,id',
            'type' => 'required|string',
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'position' => 'nullable|array',
            'size' => 'nullable|array',
            'style' => 'nullable|json',
            'settings' => 'nullable|json',
            'is_active' => 'boolean',
        ];
    }
}
