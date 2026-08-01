<?php

namespace App\Http\Requests\Client;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
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
            'name' => ['nullable', 'string'],
            'appearance_date' => ['nullable', 'date'],
        ];
    }

    public function withValidator($validator)
    {
        $allData = $this->all();

        $validator->after(function($validator) use ($allData) {
           if(empty($allData)) {
               $validator->errors()->add('', 'No fields are filled in');
           }
        });
    }
}
