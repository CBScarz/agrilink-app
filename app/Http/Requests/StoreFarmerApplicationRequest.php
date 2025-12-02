<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFarmerApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'business_name' => ['required', 'string', 'max:255'],
            'business_permit_url' => ['required', 'string', 'url'],
            'location' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'business_name.required' => 'The business name is required for farmer registration.',
            'business_permit_url.required' => 'A valid business permit URL must be provided.',
            'business_permit_url.url' => 'The business permit URL must be a valid URL.',
            'location.required' => 'The farmer location is required.',
        ];
    }
}
