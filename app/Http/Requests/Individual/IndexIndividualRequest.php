<?php

namespace App\Http\Requests\Individual;

use App\Enums\Sex;
use App\Models\Individual;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexIndividualRequest extends FormRequest
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
            'search' => ['nullable', 'string'],
            'sort' => ['nullable', 'string'],
            'sex' => ['nullable', Rule::enum(Sex::class)],
            'age' => ['nullable', 'integer', 'min:18', 'max:65'],
            'per_page' => ['nullable', 'integer', 'min: 1', 'max:100']
        ];
    }

    protected function prepareForValidation()
    {
        $inputtedSex = Sex::tryFrom(mb_strtolower($this->input('sex')));

        if(empty($inputtedSex)) {
            return;
        }

        if(empty($inputtedSex)) abort(422, "An incorrect value was entered");

        $this->merge([
            'sex' => $inputtedSex->value
        ]);
    }
}
