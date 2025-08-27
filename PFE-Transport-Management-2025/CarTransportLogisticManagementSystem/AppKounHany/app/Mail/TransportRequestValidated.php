<?php

namespace App\Mail;

use App\Models\Transport;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransportRequestValidated extends Mailable
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
        return $this->subject("Transport : {$this->transport->chassis} Validée par : {$this->transport->chefUser->name}")
            ->view('emails.transport_validated');
    }
}