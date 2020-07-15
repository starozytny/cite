<?php


namespace App\Service;

use App\Entity\TicketResponsable;
use Doctrine\ORM\EntityManagerInterface;

class TicketGenerator
{    
    private $barcodeDirectory;

    public function __construct($barcodeDirectory)
    {
        $this->barcodeDirectory = $barcodeDirectory;
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

    public function getBarcodeDirectory()
    {
        return $this->barcodeDirectory;
    }
}