<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQrCodeRequest extends FormRequest
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
            'slug' => 'required|string|unique:qr_codes,slug,' . $this->qrCode->id,
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:static,dynamic',
            'format' => 'required|in:url,text,email,phone,sms,vcard',
            'payload' => 'nullable|array',
            'options' => 'nullable|array',
            'is_active' => 'boolean',
        ];
    }
}
