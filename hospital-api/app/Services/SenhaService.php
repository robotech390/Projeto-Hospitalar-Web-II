<?php

namespace App\Services;

use App\Models\TokenRedefinicaoSenha;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * SenhaService — gestão de senhas e envio de e-mails relacionados.
 *
 * Responsável por:
 *  - Gerar senhas aleatórias seguras
 *  - Enviar e-mail de boas-vindas com senha de primeiro acesso
 *  - Reenviar senha de primeiro acesso (gera nova e reseta primeiro_acesso)
 *  - Gerar token e enviar e-mail de redefinição (esqueci minha senha)
 */
class SenhaService
{
    private const MINUTOS_VALIDADE_TOKEN = 60;

    private const ROTULOS_FUNCAO = [
        'administrador' => 'Administrador',
        'medico'        => 'Médico(a)',
        'farmaceutico'  => 'Farmacêutico(a)',
        'recepcionista' => 'Recepcionista',
        'paciente'      => 'Paciente',
    ];

    public static function gerarSenhaPrimeiroAcesso(): string
    {
        $maiuscula = strtoupper(Str::random(2));
        $numero    = (string) random_int(10, 99);
        $simbolo   = str_shuffle('!@#$%')[0];
        $resto     = Str::random(7);
        return str_shuffle($maiuscula . $numero . $simbolo . $resto);
    }

    public static function enviarSenhaPrimeiroAcesso(
        string $email,
        string $nome,
        string $funcao,
        string $senha
    ): void {
        Mail::send([], [], function ($message) use ($email, $nome, $funcao, $senha) {
            $message
                ->to($email, $nome)
                ->subject('Saúde+Vc — Bem-vindo(a) ao sistema')
                ->html(self::templateBoasVindas($nome, $funcao, $senha));
        });
    }

    /**
     * Reenvia a senha de primeiro acesso: gera nova, atualiza o usuário e envia por e-mail.
     * Use quando o usuário perdeu a senha temporária ou foi suspenso e voltou.
     */
    public static function reenviarSenhaPrimeiroAcesso(Usuario $usuario): string
    {
        $novaSenha = self::gerarSenhaPrimeiroAcesso();

        $usuario->update([
            'senha'           => Hash::make($novaSenha),
            'primeiro_acesso' => true,
        ]);

        self::enviarSenhaPrimeiroAcesso(
            $usuario->email,
            $usuario->usuario,
            $usuario->funcao,
            $novaSenha
        );

        return $novaSenha;
    }

    public static function gerarEnviarTokenRedefinicao(string $email, string $nome): string
    {
        // Invalida tokens anteriores do mesmo e-mail ainda não utilizados
        TokenRedefinicaoSenha::where('email', $email)
            ->whereNull('utilizado_em')
            ->update(['utilizado_em' => now()]);

        $token = Str::random(64);

        TokenRedefinicaoSenha::create([
            'email'     => $email,
            'token'     => $token,
            'expira_em' => now()->addMinutes(self::MINUTOS_VALIDADE_TOKEN),
        ]);

        $linkRedefinicao = rtrim(config('app.frontend_url', 'http://localhost:3000'), '/')
            . '/redefinir-senha?token=' . $token . '&email=' . urlencode($email);

        Mail::send([], [], function ($message) use ($email, $nome, $linkRedefinicao) {
            $message
                ->to($email, $nome)
                ->subject('Saúde+Vc — Redefinição de senha')
                ->html(self::templateRedefinicaoSenha($nome, $linkRedefinicao));
        });

        return $token;
    }

    // ─── Templates de e-mail ───────────────────────────────────────────────────
    //
    // Layout email-safe: usa tables, inline styles e cores corporativas.
    // Compatível com Gmail, Outlook, Apple Mail, Thunderbird.

    private static function templateBoasVindas(string $nome, string $funcao, string $senha): string
    {
        $rotuloFuncao = self::ROTULOS_FUNCAO[$funcao] ?? ucfirst($funcao);
        $urlSistema   = config('app.frontend_url', config('app.url'));
        $ano          = date('Y');

        return <<<HTML
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>Bem-vindo(a) ao Saúde+Vc</title>
        </head>
        <body style="margin:0;padding:0;background:#f5f6fa;font-family:-apple-system,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#1e293b;">
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f5f6fa;">
            <tr>
              <td align="center" style="padding:40px 16px;">
                <table role="presentation" width="560" cellspacing="0" cellpadding="0" border="0" style="max-width:560px;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(15,61,56,0.08);">

                  <tr>
                    <td style="background:#0f3d38;padding:32px 40px;">
                      <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                          <td style="vertical-align:middle;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="background:#2a9d8f;border-radius:10px;width:44px;height:44px;">
                              <tr><td align="center" style="color:#ffffff;font-size:22px;font-weight:700;line-height:44px;">+</td></tr>
                            </table>
                          </td>
                          <td style="padding-left:14px;vertical-align:middle;">
                            <p style="margin:0;color:#ffffff;font-size:20px;font-weight:700;letter-spacing:-0.3px;">Saúde+Vc</p>
                            <p style="margin:2px 0 0;color:rgba(255,255,255,0.6);font-size:12px;">Sistema Hospitalar</p>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>

                  <tr>
                    <td style="padding:40px;">
                      <h1 style="margin:0 0 12px;font-size:22px;color:#0f3d38;font-weight:600;line-height:1.3;">
                        Bem-vindo(a), {$nome}!
                      </h1>
                      <p style="margin:0 0 28px;color:#64748b;font-size:14px;line-height:1.6;">
                        Seu acesso ao Saúde+Vc foi criado com o perfil de <strong style="color:#0f3d38;">{$rotuloFuncao}</strong>.
                        Use a senha temporária abaixo no seu primeiro acesso.
                      </p>

                      <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f0fdfa;border:1px solid #99f6e4;border-radius:12px;">
                        <tr>
                          <td style="padding:24px;text-align:center;">
                            <p style="margin:0 0 8px;color:#0f3d38;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:1.5px;">
                              Senha de primeiro acesso
                            </p>
                            <p style="margin:0;font-family:'Courier New',monospace;font-size:24px;color:#0f3d38;font-weight:700;letter-spacing:3px;">
                              {$senha}
                            </p>
                          </td>
                        </tr>
                      </table>

                      <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:24px 0 0;">
                        <tr>
                          <td style="background:#fef3c7;border-left:3px solid #f59e0b;border-radius:6px;padding:14px 16px;">
                            <p style="margin:0;color:#92400e;font-size:13px;line-height:1.5;">
                              <strong>Importante:</strong> por segurança, você precisará criar uma senha pessoal no seu primeiro acesso.
                            </p>
                          </td>
                        </tr>
                      </table>

                      <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" style="margin:32px auto 0;">
                        <tr>
                          <td align="center" style="background:#2a9d8f;border-radius:10px;">
                            <a href="{$urlSistema}" target="_blank" style="display:inline-block;padding:14px 36px;color:#ffffff;text-decoration:none;font-weight:500;font-size:14px;">
                              Acessar o sistema →
                            </a>
                          </td>
                        </tr>
                      </table>

                      <p style="margin:32px 0 0;padding-top:24px;border-top:1px solid #e2e8f0;color:#94a3b8;font-size:12px;line-height:1.6;">
                        Se você não esperava este e-mail, por favor ignore-o.
                        <br>Em caso de dúvidas, entre em contato com a administração do hospital.
                      </p>
                    </td>
                  </tr>

                  <tr>
                    <td style="background:#f8fafc;padding:18px 40px;text-align:center;border-top:1px solid #e2e8f0;">
                      <p style="margin:0;color:#94a3b8;font-size:11px;">
                        © {$ano} Saúde+Vc — IFSC Tubarão · E-mail automático, não responda.
                      </p>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </body>
        </html>
        HTML;
    }

    private static function templateRedefinicaoSenha(string $nome, string $link): string
    {
        $ano = date('Y');

        return <<<HTML
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>Redefinição de senha — Saúde+Vc</title>
        </head>
        <body style="margin:0;padding:0;background:#f5f6fa;font-family:-apple-system,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#1e293b;">
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f5f6fa;">
            <tr>
              <td align="center" style="padding:40px 16px;">
                <table role="presentation" width="560" cellspacing="0" cellpadding="0" border="0" style="max-width:560px;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(15,61,56,0.08);">

                  <tr>
                    <td style="background:#0f3d38;padding:32px 40px;">
                      <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                          <td style="vertical-align:middle;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="background:#2a9d8f;border-radius:10px;width:44px;height:44px;">
                              <tr><td align="center" style="color:#ffffff;font-size:22px;font-weight:700;line-height:44px;">+</td></tr>
                            </table>
                          </td>
                          <td style="padding-left:14px;vertical-align:middle;">
                            <p style="margin:0;color:#ffffff;font-size:20px;font-weight:700;letter-spacing:-0.3px;">Saúde+Vc</p>
                            <p style="margin:2px 0 0;color:rgba(255,255,255,0.6);font-size:12px;">Redefinição de senha</p>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>

                  <tr>
                    <td style="padding:40px;">
                      <h1 style="margin:0 0 12px;font-size:22px;color:#0f3d38;font-weight:600;line-height:1.3;">
                        Olá, {$nome}
                      </h1>
                      <p style="margin:0 0 28px;color:#64748b;font-size:14px;line-height:1.6;">
                        Recebemos uma solicitação para redefinir sua senha de acesso ao Saúde+Vc.
                        Clique no botão abaixo para criar uma nova senha:
                      </p>

                      <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" style="margin:0 auto;">
                        <tr>
                          <td align="center" style="background:#2a9d8f;border-radius:10px;">
                            <a href="{$link}" target="_blank" style="display:inline-block;padding:14px 36px;color:#ffffff;text-decoration:none;font-weight:500;font-size:14px;">
                              Redefinir minha senha
                            </a>
                          </td>
                        </tr>
                      </table>

                      <p style="margin:28px 0 8px;color:#94a3b8;font-size:12px;">
                        Ou copie e cole este link no seu navegador:
                      </p>
                      <p style="margin:0;background:#f1f5f9;padding:12px;border-radius:8px;font-size:11px;color:#475569;word-break:break-all;font-family:monospace;">
                        {$link}
                      </p>

                      <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:24px 0 0;">
                        <tr>
                          <td style="background:#fef2f2;border-left:3px solid #ef4444;border-radius:6px;padding:14px 16px;">
                            <p style="margin:0;color:#991b1b;font-size:13px;line-height:1.5;">
                              Este link expira em <strong>60 minutos</strong>.
                            </p>
                          </td>
                        </tr>
                      </table>

                      <p style="margin:32px 0 0;padding-top:24px;border-top:1px solid #e2e8f0;color:#94a3b8;font-size:12px;line-height:1.6;">
                        Se você não solicitou esta redefinição, ignore este e-mail.
                        <br>Sua senha permanecerá inalterada.
                      </p>
                    </td>
                  </tr>

                  <tr>
                    <td style="background:#f8fafc;padding:18px 40px;text-align:center;border-top:1px solid #e2e8f0;">
                      <p style="margin:0;color:#94a3b8;font-size:11px;">
                        © {$ano} Saúde+Vc — IFSC Tubarão · E-mail automático, não responda.
                      </p>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </body>
        </html>
        HTML;
    }
}
