<?php


namespace App\Service;

use App\Entity\TicketCreneau;
use App\Entity\TicketDay;
use App\Entity\TicketProspect;
use App\Entity\TicketResponsable;
use Doctrine\ORM\EntityManagerInterface;
use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;
use Twig\Environment;

class TicketGenerator
{    
    private $barcodeDirectory;
    private $publicDirectory;
    private $twig;

    public function __construct($barcodeDirectory, $publicDirectory, Environment $twig)
    {
        $this->barcodeDirectory = $barcodeDirectory;
        $this->publicDirectory = $publicDirectory;
        $this->twig = $twig;
    }

    public function getPrefix(TicketResponsable $responsable){
        return $responsable->getId() . substr(strtoupper($responsable->getFirstname()), 0, 1) . substr(strtoupper($responsable->getLastname()), 0, 1);
    }

    public function generate(TicketResponsable $responsable)
    {
        $uniq = hexdec(uniqid());
        $ticket = $this->getPrefix($responsable) . substr($uniq, strlen($uniq) - 5, 5);
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        if(!is_dir($this->getBarcodeDirectory())){
            mkdir($this->getBarcodeDirectory());
        }
        $generatorr = file_put_contents($this->getBarcodeDirectory() . '/' .$responsable->getId() . '-barcode.png', $generator->getBarcode($ticket, $generator::TYPE_CODE_128));




        return $ticket;
    }

    public function createFileTicket($file, TicketResponsable $responsable, TicketDay $day, TicketCreneau $creneau, $prospects){
        $mpdf = new Mpdf();

        $img = file_get_contents($file);
        $data = base64_encode($img);

        $mpdf->SetTitle('Ticket cité de la musique - ' . $responsable->getFirstname() . ' ' . $responsable->getLastname());
        $stylesheet = file_get_contents($this->getPublicDirectory() . '/public/pdf/css/bootstrap.min.css');
        $stylesheet2 = file_get_contents($this->getPublicDirectory() . '/public/pdf/css/custom-pdf.css');
        $mpdf->WriteHTML($stylesheet,HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($stylesheet2,HTMLParserMode::HEADER_CSS);
        $mpdf->SetProtection(array(
            'print'
        ),'', 'Pf3zGgig5hy5');

        $mpdf->WriteHTML(
            $this->twig->render('root/app/pdf/ticket.html.twig', ['day' => $day, 'creneau' => $creneau, 'responsable' => $responsable, 'prospects' => $prospects, 'image' => $data]),
            HTMLParserMode::HTML_BODY
        );

        return $mpdf;
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