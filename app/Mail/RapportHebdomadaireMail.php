<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class RapportHebdomadaireMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private string $filePath) {}

    public function build()
    {
        return $this->subject('Rapport hebdomadaire des dépenses')
            ->view('email.rapport')
            ->attach($this->filePath);
    }
}
