<?php

namespace App\Http\Requests\Individual;

use App\Enums\Sex;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIndividualRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'sex' => ['required', 'string', Rule::enum(Sex::class)],
            'birth_date' => ['nullable', 'date'], // 10.06 сделать обязательным полем.
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
        ];
    }

    public function prepareForValidation()
    {
        $inputtedSex = Sex::tryFrom(mb_strtolower($this->input('sex')));

        $this->merge([
            'sex' => $inputtedSex->value
        ]);
    }
}
