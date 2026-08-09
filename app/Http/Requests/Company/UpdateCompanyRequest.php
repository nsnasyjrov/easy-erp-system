<?php

namespace App\Http\Requests\Company;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return True;
        // return auth->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string'],
            'legal_address' => ['nullable', 'string'],
            'registration_country' => ['nullable', 'string'],
            'chief_manager' => ['nullable', 'integer', 'exists:users,id'],
            'tin_number' => ['nullable', 'string']
        ];
    }
}
