<?php

namespace App\Http\Requests\Client;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('client'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string'],
            'appearance_date' => ['sometimes', 'date'],
        ];
    }

    public function after(): array
    {
        return [

            function (Validator $validator): void {
                if($validator->errors()->isNotEmpty()) {
                    return;
                }

                if($validator->safe()->all() === []) {
                    $validator->errors()->add('request', 'At least one field must be provided for update.');
                }
            }
        ];
    }
}
