<?php
namespace App\Mail;

use App\Models\GaragePartenaire;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GarageApprouveMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public GaragePartenaire $garage) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre compte Garage Partenaire NOVAuto a été approuvé !',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.garage-approuve',
        );
    }
}