<?php

namespace App\Http\Requests\Company;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
        //return auth()->check(); TODO: когда сделаешь норм. авторизацию снеси сверху и расскоментируй.
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'legal_address' => ['nullable', 'string', 'max:255'],
            'registration_country' => ['nullable', 'string', 'max:255'],
            'tin_number' => ['required', 'string', 'max:255', Rule::unique('companies', 'tin_number')],
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'chief_manager' => ['nullable', 'integer'],

            'contact_type' => ['nullable', 'string', 'max:255'],
            'contact_value' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'tin_number.unique' => "Company with this TIN already exists."
        ];
    }
}
