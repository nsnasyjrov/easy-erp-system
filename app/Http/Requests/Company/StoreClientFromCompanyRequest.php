<?php

namespace App\Http\Requests\Company;

use App\Enums\ContactInfoType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientFromCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return True;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'appearance_date' => ['required', 'date'],

            'contacts' => ['required', 'array'],
            'contacts.*.type' => ['required', Rule::enum(ContactInfoType::class)],
            'contacts.*.value' => ['required', 'string', 'max:255']
        ];
    }
}
