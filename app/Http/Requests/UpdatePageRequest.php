<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePageRequest extends FormRequest
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
        $pageId = $this->route('page')->id;
        
        return [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/|unique:pages,slug,' . $pageId,
            'description' => 'nullable|string',
            'style' => 'nullable|array',
            'settings' => 'nullable|array',
            'is_active' => 'boolean',
            'published_at' => 'nullable|date',
        ];
    }
}
