<?php


namespace App\Service;

use App\Entity\TicketResponsable;
use Doctrine\ORM\EntityManagerInterface;

class TicketGenerator
{    
    public function getPrefix(TicketResponsable $responsable){
        return $responsable->getId() . substr(strtoupper($responsable->getFirstname()), 0, 1) . substr(strtoupper($responsable->getLastname()), 0, 1);
    }

    public function generate(TicketResponsable $responsable)
    {
        $uniq = strtoupper(uniqid());
        return $this->getPrefix($responsable) . substr($uniq, strlen($uniq) - 5, 5);
    }
}