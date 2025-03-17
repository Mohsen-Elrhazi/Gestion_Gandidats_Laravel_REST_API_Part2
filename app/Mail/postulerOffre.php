<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class postulerOffre extends Mailable
{
    use Queueable, SerializesModels;

    public $recruteur;
    public $candidat;
    public $cvFilePath;
    public $offre;

    /**
     * Create a new message instance.
     */
    public function __construct($recruteur, $candidat, $cvFilePath, $offre)
    {
        $this->recruteur = $recruteur;
        $this->candidat = $candidat;
        $this->cvFilePath = $cvFilePath;
        $this->offre = $offre;
    }

    public function build()
    {
        return $this->to($this->recruteur->email)
                    ->replyTo($this->candidat->email)
                    ->subject("Nouvelle candidature pour l'offre : {$this->offre->title}")
                    ->html(
                        "<html><body>
                            <h1>Bonjour {$this->recruteur->name},</h1>
                            <p>{$this->candidat->name} a postulé à votre offre <strong>{$this->offre->title}</strong>.</p>
                            <p>Vous trouverez son CV en pièce jointe.</p>
                            <br>
                            <p>Cordialement,</p>
                            <p>L'équipe de recrutement</p>
                        </body></html>"
                    )
                    ->attach($this->cvFilePath);
    }


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Postuler Offre',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}