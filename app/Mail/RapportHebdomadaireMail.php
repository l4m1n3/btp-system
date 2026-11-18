<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Carbon;

class RapportHebdomadaireMail extends Mailable
{
    use Queueable;

    public function __construct(public string $pdf, public Carbon $debut, public Carbon $fin) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Rapport hebdomadaire du '.$this->debut->format('d/m').' au '.$this->fin->format('d/m/Y'));
    }

    public function content(): Content
    {
        return new Content(view: 'mail.rapport-hebdomadaire');
    }

    public function attachments(): array
    {
        return [Attachment::fromData(fn () => $this->pdf, 'rapport-hebdomadaire-'.$this->debut->format('Y-m-d').'.pdf')->withMime('application/pdf')];
    }
}
