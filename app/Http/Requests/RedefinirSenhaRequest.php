<?php
namespace App\Http\Requests;
class RedefinirSenhaRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return ['email' => ['required', 'email', 'exists:usuarios,email'], 'codigo' => ['required', 'string', 'size:6'], 'nova_senha' => ['required', 'string', 'min:8', 'confirmed']];
    }
    public function messages(): array
    {
        return ['codigo.size' => 'O codigo deve conter exatamente 6 caracteres.'];
    }
}