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

    /**
     * Create a new message instance.
     */
    public function __construct(string $codigo)
    {
        $this->codigo = $codigo;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Código de Recuperação de Senha');
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: "<h3>Recuperação de Senha</h3><p>Seu código de verificação é: <strong>{$this->codigo}</strong></p>"
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}