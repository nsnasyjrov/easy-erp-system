<?php

namespace App\Http\Requests\Individual;

use App\Enums\Sex;
use App\Utils\IndividualUtils;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIndividualRequest extends FormRequest
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
            'first_name' => ['nullable', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'sex' => ['nullable', Rule::enum(Sex::class)],
            'birth_date' => ['nullable', 'date']
        ];
    }

    protected function prepareForValidation()
    {
        IndividualUtils::mergeSex($this);
    }
}
