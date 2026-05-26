<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
class CodigoRecuperacaoMail extends Mailable
{
    use Queueable, SerializesModels;
    public string $codigo;
    public function __construct(string $codigo)
    {
        $this->codigo = $codigo;
    }
    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Codigo de Recuperacao de Senha');
    }
    public function content(): Content
    {
        return new Content(htmlString: "<h3>Recuperacao de Senha</h3><p>Seu codigo de verificacao e: <strong>{$this->codigo}</strong></p>");
    }
    public function attachments(): array
    {
        return [];
    }
}