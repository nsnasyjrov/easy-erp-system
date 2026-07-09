<?php

namespace App\Http\Requests\Company;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexCompanyRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'has_client' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'string'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100']
        ];
    }

    protected function prepareForValidation()
    {
        if(empty($this->input('has_client'))) return;

        $this->merge([
            'has_client' => filter_var($this->has_client, FILTER_VALIDATE_BOOLEAN)
        ]);
    }
}
