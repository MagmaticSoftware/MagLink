<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQrCodeRequest extends FormRequest
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
            'slug' => 'required|string|min:3|max:100|unique:qr_codes,slug|regex:/^[a-z0-9]+(-[a-z0-9]+)*$/',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:static,dynamic',
            'format' => 'required|in:url,text,email,phone,sms,vcard',
            'payload' => 'nullable|array',
            'options' => 'nullable|array',
            'is_active' => 'boolean',
            'require_consent' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'slug.regex' => 'Lo slug deve contenere solo lettere minuscole, numeri e trattini. Non può iniziare o finire con un trattino.',
            'slug.min' => 'Lo slug deve contenere almeno :min caratteri.',
            'slug.max' => 'Lo slug non può superare :max caratteri.',
            'slug.unique' => 'Questo slug è già in uso. Scegline un altro.',
        ];
    }
}
