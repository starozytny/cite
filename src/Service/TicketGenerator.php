<?php


namespace App\Service;

use App\Entity\TicketCreneau;
use App\Entity\TicketDay;
use App\Entity\TicketProspect;
use App\Entity\TicketResponsable;
use Doctrine\ORM\EntityManagerInterface;
use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;
use Mpdf\Output\Destination;
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

    public function generate(TicketResponsable $responsable, $prospects)
    {
        $uniq = time();
        $ticket = $responsable->getId() . $uniq;
        $generator = new \Picqer\Barcode\BarcodeGeneratorJPG();
        if(!is_dir($this->getBarcodeDirectory())){
            mkdir($this->getBarcodeDirectory());
        }
        $fileImage = $this->getBarcodeDirectory() . '/' .$responsable->getId() . '-barcode.jpg';
        $generatorr = file_put_contents($fileImage, $generator->getBarcode($ticket, $generator::TYPE_CODE_128));

        $pdfDirectory = $this->getBarcodeDirectory() . '/pdf';
        if(!is_dir($pdfDirectory)){
            mkdir($pdfDirectory);
        }

        $creneau = $responsable->getCreneau();
        $day = $creneau->getTicketDay();
        $mpdf = $this->createFileTicket($fileImage, $responsable, $day, $creneau, $prospects, $ticket);

        $mpdf->Output($pdfDirectory . '/' . $ticket . '-ticket.pdf', Destination::FILE);

        return $ticket;
    }

    public function createFileTicket($fileImage, TicketResponsable $responsable, TicketDay $day, TicketCreneau $creneau, $prospects, $ticket){
        $mpdf = new Mpdf();

        $img = file_get_contents($fileImage);
        $data = base64_encode($img);

        dump($data);

        $mpdf->SetTitle('Ticket cité de la musique - ' . $responsable->getFirstname() . ' ' . $responsable->getLastname());
        $stylesheet = file_get_contents($this->getPublicDirectory() . '/public/pdf/css/bootstrap.min.css');
        $stylesheet2 = file_get_contents($this->getPublicDirectory() . '/public/pdf/css/custom-pdf.css');
        $mpdf->WriteHTML($stylesheet,HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($stylesheet2,HTMLParserMode::HEADER_CSS);
        $mpdf->SetProtection(array(
            'print'
        ),'', 'Pf3zGgig5hy5');

        $mpdf->WriteHTML(
            $this->twig->render('root/app/pdf/ticket.html.twig', ['day' => $day, 'creneau' => $creneau, 'responsable' => $responsable, 'prospects' => $prospects, 'image' => $data, 'ticket' => $ticket]),
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