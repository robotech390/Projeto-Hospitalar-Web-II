<?php
namespace App\Http\Requests;
class EsqueciSenhaRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return ['email' => ['required', 'email', 'exists:usuarios,email']];
    }
    public function messages(): array
    {
        return ['email.required' => 'O campo e-mail e obrigatorio.', 'email.email' => 'Informe um e-mail valido.', 'email.exists' => 'Este e-mail nao esta cadastrado.'];
    }
}