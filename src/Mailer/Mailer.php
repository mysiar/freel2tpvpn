<?php
declare(strict_types=1);

namespace App\Mailer;

use Swift_Mailer;
use Swift_Message;

class Mailer extends Swift_Mailer
{
    public function __construct(MailerTransport $transport)
    {
        parent::__construct($transport);
    }

    public function sendMessage(string $email, string $subject, string $body): int
    {
        $message = new Swift_Message($subject);
        $message->setFrom([$_ENV['SENDER_EMAIL'] => $_ENV['SENDER_TITLE']]);
        $message->setTo([$email]);
        $message->setBody($body);
        return $this->send($message);
    }
}
