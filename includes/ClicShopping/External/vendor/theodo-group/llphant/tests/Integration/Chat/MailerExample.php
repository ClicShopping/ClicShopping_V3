<?php

declare(strict_types=1);

namespace Tests\Integration\Chat;

class MailerExample
{
    public string $lastMessage = '';

    /**
     * send an email
     */
    public function sendMail(string $subject, string $body, string $email): string
    {
        $this->lastMessage = 'The email has been sent to '.$email.' with the subject '.$subject.' and the body '.$body.'.';

        return $this->lastMessage;
    }
}
