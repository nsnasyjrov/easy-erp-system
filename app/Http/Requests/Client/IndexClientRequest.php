<?php

namespace App\Http\Requests\Client;

use App\Enums\ClientType;
use App\Models\Client;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', Client::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:256'],
            'type' => ['nullable', Rule::enum(ClientType::class)],
            'sort' => ['nullable', 'string'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100']
        ];
    }

    protected function prepareForValidation()
    {
        if(empty($this->input('type'))) return;

        $clientType = ClientType::tryFrom(mb_strtolower($this->input('type')));

        if(empty($clientType)) abort(404, "There is no such client type");

        $this->merge([
            'type' => $clientType->value
        ]);
    }

}
