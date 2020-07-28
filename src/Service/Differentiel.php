<?php


namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class Differentiel
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function diff($prospect, $adh){
        $p_resp = $prospect->getResponsable();
        $a_resp = $adh->getPersonne();
        if($p_resp->getFirstname() != $a_resp->getFirstname() || $p_resp->getLastname() != $a_resp->getLastname()
           || $p_resp->getEmail() != $a_resp->getEmail() || $p_resp->getCp() != $a_resp->getCp()){
            return true;
        }
        return false;
    }
}