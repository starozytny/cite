<?php


namespace App\Service;


use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class Mailer
{
    private $mailer;
    private $barcodeDirectory;
    private $publicDirectory;

    public function __construct($barcodeDirectory, $publicDirectory, MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        $this->barcodeDirectory = $barcodeDirectory;
        $this->publicDirectory = $publicDirectory;
    }

    public function sendMail($title, $text, $html, $params, $email,  $file = null, $responsable=null, $from = 'inscriptions@citemusique-marseille.com')
    {
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($email)
            ->subject($title)
            ->text($text)
            ->htmlTemplate($html)
            ->context($params)
        ;

        if($file != null){
//            $email->attachFromPath($file);
            $email->embed(fopen($this->getBarcodeDirectory() . '/' .$responsable->getId() . '-barcode.jpg', 'r'), 'barcode.jpg');
            $email->embed(fopen($this->getPublicDirectory() . '/public/logo-ca-little.png', 'r'), 'logo.png');
        }

        if($this->mailer->send($email)){
            return true;
        } else {
            return 'Le message n\'a pas pu être délivré. Veuillez contacter le support.';
        }
    }

    public function getBarcodeDirectory()
    {
        return $this->barcodeDirectory;
    }

    public function getPublicDirectory()
    {
        return $this->publicDirectory;
    }
}
