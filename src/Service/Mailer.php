<?php


namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class Mailer
{
    private $mailer;

    public function __construct(MailerInterface $mailer, EntityManagerInterface $em)
    {
        $this->mailer = $mailer;
    }

    public function sendMail($title, $text, $html, $params, $email,  $file = null, $from = 'chanbora@logilink.fr')
    {
        $email = (new TemplatedEmail())
            ->from($from)
            ->cc($email)
            ->subject($title)
            ->text($text)
            ->htmlTemplate($html)
            ->context($params)
        ;

        if($file != null){
            $email->attachFromPath($file);
        }

        if($this->mailer->send($email)){
            return true;
        } else {
            return 'Le message n\'a pas pu être délivré. Veuillez contacter le support.';
        }
    }
}
