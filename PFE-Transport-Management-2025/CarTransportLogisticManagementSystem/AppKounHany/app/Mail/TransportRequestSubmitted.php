<?php

namespace App\Mail;

use App\Models\Transport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransportRequestSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $transport;

    public function __construct(Transport $transport)
    {
        $this->transport = $transport;
    }

    public function build()
    {
        return $this->subject("Nouvelle Demande : #{$this->transport->chassis} ({$this->transport->nameUser->name})")
                    ->view('emails.transport_submitted');
    }
}
