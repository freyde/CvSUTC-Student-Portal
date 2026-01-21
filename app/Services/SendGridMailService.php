<?php

namespace App\Services;

use SendGrid;
use SendGrid\Mail\Mail;

class SendGridMailService
{
    public function send($to, $subject, $content)
    {
        $email = new Mail();
        $email->setFrom(
            config('mail.from.address'),
            config('mail.from.name')
        );
        $email->setSubject($subject);
        $email->addTo($to);
        $email->addContent('text/plain', $content);

        $sendgrid = new SendGrid(env('SENDGRID_API_KEY'));

        return $sendgrid->send($email);
    }
}

