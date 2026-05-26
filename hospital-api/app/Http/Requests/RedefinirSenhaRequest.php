<?php

namespace App\Http\Requests;

class RedefinirSenhaRequest extends BaseRequest
{
    /**
     * Determine if    the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'email'      => ['required', 'email', 'exists:usuarios,email'],
            'codigo'     => ['required', 'string', 'size:6'],
            'nova_senha' => ['required', 'string', 'min:8', 'confirmed']
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'codigo.size' => 'O código deve conter exatamente 6 caracteres.'
        ];
    }
}