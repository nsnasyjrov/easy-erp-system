<?php

namespace App\Http\Requests\Individual;

use App\Enums\Sex;
use App\Models\Individual;
use App\Utils\IndividualUtils;
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
            'min_age' => ['integer', 'min:18', 'max:65'],
            'max_age' => ['integer', 'min:18', 'max:65', 'gt:min_age'],
            'per_page' => ['nullable', 'integer', 'min: 1', 'max:100']
        ];
    }

    protected function prepareForValidation()
    {
        IndividualUtils::mergeSex($this);
    }
}
