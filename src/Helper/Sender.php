<?php

namespace App\Helper;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class Sender {

    public function __construct(private MailerInterface $mailer) {
    }


    public function sendNotif(string $dest, string $subject, string $text): void
    {
        $email = new Email();
        $email->subject($subject)
            ->text($text)
            ->from('no-reply@serie.com')
            ->to($dest);


        $this->mailer->send($email);
    }

}