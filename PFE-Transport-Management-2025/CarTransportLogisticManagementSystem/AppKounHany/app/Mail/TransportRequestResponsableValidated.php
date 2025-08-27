<?php

namespace App\Mail;

use App\Models\Transport;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransportRequestResponsableValidated extends Mailable
{
    use Queueable, SerializesModels;

    public $transport;
    public $validator;
    public $recipientRole;

    public function __construct(Transport $transport, User $validator, string $recipientRole)
    {
        $this->transport = $transport;
        $this->validator = $validator;
        $this->recipientRole = $recipientRole;
    }

    public function build()
    {
        return $this->subject("Demande Validée par {$this->transport->responsableUser->name} chassis : {$this->transport->chassis}")
            ->view('emails.transport_responsable_validated');
    }
}