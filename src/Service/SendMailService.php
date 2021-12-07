<?php

namespace App\Service;

use App\Service\Log;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

class SendMailService
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new Mailer(Transport::fromDsn($_ENV['MAILER_DSN']));
    }


    public function sendEmail($from, $to, $subject, $message)
    {
        try {
            $email = (new Email())
                ->from($from)
                ->to($to)
                ->priority(Email::PRIORITY_HIGH)
                ->subject($subject)
                ->html($message);

            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $logger = Log::getInstance();
            $logger->error("Message not send. Code Error: " . $e->getCode() . " Message: " . $e->getMessage());
        }
    }
}
