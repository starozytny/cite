<?php


namespace App\Service;

use App\Entity\TicketDay;
use App\Entity\TicketResponsable;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class ResponsableService
{
    private $em;
    private $checkTime;
    private $remaining;

    public function __construct(EntityManagerInterface $em, CheckTime $checkTime, Remaining $remaining)
    {
        $this->em = $em;
        $this->checkTime = $checkTime;
        $this->remaining = $remaining;
    }

    public function deleteNonConfirmed()
    {
        $responsables = $this->em->getRepository(TicketResponsable::class)->findBy(array(
            'status' => TicketResponsable::ST_TMP,
            'isWaiting' => false
        ));

        foreach($responsables as $responsable){
            if($this->checkTime->moreFiveMinutes($responsable->getCreateAt())){
                $this->deleteResponsable($responsable);
            }
        }      
    }

    public function deleteResponsable($responsable)
    {
        $prospects = $responsable->getProspects();
        $nbProspects = count($prospects);
        foreach ($prospects as $prospect){
            if(!$responsable->getIsWaiting()){
                $creneau = $prospect->getCreneau();
                $day = $creneau->getTicketDay();
            }
            $this->em->remove($prospect);
        }

        if(!$responsable->getIsWaiting()){
            $this->remaining->increaseRemaining($day, $creneau, $nbProspects);
        }

        $this->em->remove($responsable);
        $this->em->flush();
    }

     /**
     * Create Ticket Responsable
     */
    public function createResponsable($resp, $waiting)
    {
        return (new TicketResponsable())
            ->setFirstname($resp->firstname)
            ->setLastname($resp->lastname)
            ->setCivility($resp->civility)
            ->setEmail($resp->email)
            ->setPhoneDomicile($this->setToNullIfEmpty($resp->phoneDomicile))
            ->setPhoneMobile($this->setToNullIfEmpty($resp->phoneMobile))
            ->setAdr($resp->adr)
            ->setComplement($this->setToNullIfEmpty($resp->complement))
            ->setCp($resp->cp)
            ->setCity($resp->city)
            ->setIsWaiting($waiting)
        ;
    }

    private function setToNullIfEmpty($item){
        return $item != "" ? $item : null;
    }
}