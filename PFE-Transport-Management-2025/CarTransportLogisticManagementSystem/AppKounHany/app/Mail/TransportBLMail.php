<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransportBLMail extends Mailable
{
    use Queueable, SerializesModels;

    public $transport;
    public $subject;
    protected $fileContent;
    protected $fileName;
    protected $fileMimeType;

    public function __construct($data, $fileContent, $fileName = null)
    {
        $this->transport = $data['transport'];
        $this->subject = $data['subject'];
        $this->fileContent = $fileContent;
        $this->fileName = $fileName ?? 'bon_transfert_'.$this->transport->id.'.pdf';
        $this->fileMimeType = $this->determineMimeType($this->fileName);
    }

    public function build()
    {
        $email = $this->subject($this->subject)
                    ->view('emails.transport_bl');

        if ($this->fileContent) {
            $email->attachData($this->fileContent, $this->fileName, [
                'mime' => $this->fileMimeType,
            ]);
        }

        return $email;
    }

    protected function determineMimeType($filename)
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }
}