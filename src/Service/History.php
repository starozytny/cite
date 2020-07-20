<?php


namespace App\Service;

use App\Entity\TicketCreneau;
use App\Entity\TicketDay;
use App\Entity\TicketHistory;
use App\Entity\TicketResponsable;
use Doctrine\ORM\EntityManagerInterface;

class History
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function createHistory(TicketCreneau $creneau, TicketDay $day)
    {
        return (new TicketHistory())
            ->setFirstname('Anonyme')
            ->setLastname('Anonyme')
            ->setCivility('M')
            ->setEmail('unknow@unknow.fr')
            ->setCreneau($creneau)
            ->setDay($day)
        ;
    }

    public function updateFamille($historyId, $nb)
    {

        $hist = $this->em->getRepository(TicketHistory::class)->find($historyId);

        if(!$hist){
            return false;
        }

        $hist->setFamille($nb);
        $hist->setStep(TicketHistory::STEP_FAMILLE);

        $this->em->persist($hist);
        $this->em->flush();

        return $hist;
    }

    public function updateResp($historyId, $resp)
    {

        $hist = $this->em->getRepository(TicketHistory::class)->find($historyId);

        if(!$hist){
            return false;
        }

        $hist->setCivility($resp->civility);
        $hist->setFirstname($resp->firstname);
        $hist->setLastname($resp->lastname);
        $hist->setEmail($resp->email);
        $hist->setStep(TicketHistory::STEP_RESP);

        $this->em->persist($hist);
        $this->em->flush();

        return $hist;
    }

    public function updateTicket($historyId)
    {

        $hist = $this->em->getRepository(TicketHistory::class)->find($historyId);

        if(!$hist){
            return false;
        }

        $hist->setStep(TicketHistory::STEP_TICKET);
        $hist->setStatus(TicketHistory::STATE_CONFIRMED);

        $this->em->persist($hist);
        $this->em->flush();

        return $hist;
    }
}