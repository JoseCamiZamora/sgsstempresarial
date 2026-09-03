<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeAccessCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $nombre,
        public string $cedula,
        public string $code,
        public string $portalUrl,
        public string $companyName,
    ) {
    }

    public function build()
    {
        return $this->from(config('mail.from.address'), $this->companyName . ' - SG-SST')
            ->subject('Acceso al Portal de Firmas de ' . $this->companyName)
            ->view('emails.employee_access_credentials');
    }
}
