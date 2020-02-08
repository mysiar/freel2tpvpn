<?php
declare(strict_types=1);

namespace App\Mailer;

use Swift_SmtpTransport;

class MailerTransport extends Swift_SmtpTransport
{
    public function __construct(
        string $host = 'localhost',
        int $port = 465,
        string $encryption = 'ssl',
        string $username = '',
        string $password = ''
    ) {
        parent::__construct($host, $port, $encryption);
        $this->setUsername($username);
        $this->setPassword($password);
    }
}
