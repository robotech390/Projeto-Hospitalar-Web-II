<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * SenhaService
 *
 * Responsável por:
 *  - Gerar senhas aleatórias seguras para o primeiro acesso
 *  - Enviar a senha de primeiro acesso por e-mail ao novo usuário
 */
class SenhaService
{
    /**
     * Gera uma senha aleatória segura para o primeiro acesso.
     * A senha possui 12 caracteres contendo letras maiúsculas,
     * minúsculas, números e símbolos.
     */
    public static function gerarSenhaPrimeiroAcesso(): string
    {
        // Garante que a senha tenha ao menos 1 de cada tipo de caractere
        $maiuscula = strtoupper(Str::random(2));
        $numero    = (string) random_int(10, 99);
        $simbolo   = str_shuffle('!@#$%')[0];
        $resto     = Str::random(7);

        return str_shuffle($maiuscula . $numero . $simbolo . $resto);
    }

    /**
     * Envia a senha de primeiro acesso para o e-mail do usuário.
     *
     * @param  string  $email     Endereço de e-mail do destinatário
     * @param  string  $nome      Nome do usuário para personalizar o e-mail
     * @param  string  $funcao    Papel do usuário no sistema
     * @param  string  $senha     Senha gerada automaticamente
     */
    public static function enviarSenhaPrimeiroAcesso(
        string $email,
        string $nome,
        string $funcao,
        string $senha
    ): void {
        Mail::send([], [], function ($message) use ($email, $nome, $funcao, $senha) {
            $message
                ->to($email, $nome)
                ->subject('Sistema Hospitalar IFSC — Seu acesso foi criado')
                ->html(self::templateEmail($nome, $funcao, $senha));
        });
    }

    /**
     * Monta o HTML do e-mail de boas-vindas com a senha de primeiro acesso.
     */
    private static function templateEmail(string $nome, string $funcao, string $senha): string
    {
        $funcaoFormatada = ucfirst($funcao);
        $urlSistema      = config('app.url');

        return <<<HTML
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head><meta charset="UTF-8"><title>Acesso ao Sistema Hospitalar</title></head>
        <body style="font-family: Arial, sans-serif; background:#f5f5f5; padding:20px;">
            <div style="max-width:520px; margin:auto; background:#fff; border-radius:8px; padding:32px; border:1px solid #e0e0e0;">
                <h2 style="color:#1a5276;">Sistema Hospitalar — IFSC</h2>
                <p>Olá, <strong>{$nome}</strong>!</p>
                <p>Seu acesso ao sistema foi criado com o perfil <strong>{$funcaoFormatada}</strong>.</p>
                <p>Use as credenciais abaixo para o seu primeiro acesso:</p>
                <div style="background:#f0f4f8; padding:16px; border-radius:6px; margin:16px 0;">
                    <p style="margin:0;"><strong>Login (e-mail):</strong> este e-mail que você recebeu</p>
                    <p style="margin:8px 0 0;"><strong>Senha de primeiro acesso:</strong>
                        <code style="font-size:18px; color:#c0392b;">{$senha}</code>
                    </p>
                </div>
                <p style="color:#e74c3c;"><strong>⚠ Importante:</strong> ao fazer login pela primeira vez, você será obrigado(a) a cadastrar uma nova senha pessoal.</p>
                <a href="{$urlSistema}" style="display:inline-block; margin-top:12px; padding:12px 24px; background:#1a5276; color:#fff; text-decoration:none; border-radius:4px;">
                    Acessar o sistema
                </a>
                <p style="margin-top:24px; font-size:12px; color:#888;">
                    Se você não esperava este e-mail, entre em contato com a administração do hospital.
                </p>
            </div>
        </body>
        </html>
        HTML;
    }
}
