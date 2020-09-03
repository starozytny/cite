<?php


namespace App\Service;

use App\Entity\TicketCreneau;
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
            'isWaiting' => false,
            'isMobile' => true
        ));

        foreach($responsables as $responsable){
            if($this->checkTime->moreFiveMinutes($responsable->getCreateAt())){
                $this->deleteResponsable($responsable);
            }
        }
        $this->em->flush();
    }

    public function deleteResponsable($responsable)
    {
        if($responsable){
            $prospects = $responsable->getProspects();
            foreach ($prospects as $prospect){
                $this->em->remove($prospect);
            }

            $creneau = $responsable->getCreneau();
            $day = $creneau->getTicketDay();
            $this->remaining->increaseRemaining($day, $creneau);
            $this->em->remove($responsable);
        }
    }

     /**
     * Update Ticket Responsable
     */
    public function updateResponsable($responsableId, $resp, $waiting)
    {
        $responsable = $this->em->getRepository(TicketResponsable::class)->find($responsableId);

        if(!$responsable){
            return false;
        }

        $responsable->setFirstname($resp->firstname);
        $responsable->setLastname($resp->lastname);
        $responsable->setCivility($resp->civility);
        $responsable->setEmail($resp->email);
        $responsable->setPhoneDomicile($this->setToNullIfEmpty($resp->phoneDomicile));
        $responsable->setPhoneMobile($this->setToNullIfEmpty($resp->phoneMobile));
        $responsable->setAdr($resp->adr);
        $responsable->setComplement($this->setToNullIfEmpty($resp->complement));
        $responsable->setCp($resp->cp);
        $responsable->setCity($resp->city);
        $responsable->setIsWaiting($waiting);
        $responsable->setBrowser($_SERVER['HTTP_USER_AGENT']);

        return $responsable;
    }

    /**
     * Create TMP Responsable
     */
    public function createTmpResponsable(TicketCreneau $creneau, TicketDay $day, $isMobile = false)
    {
        return (new TicketResponsable())
            ->setFirstname('')
            ->setLastname('')
            ->setCivility('')
            ->setEmail(time())
            ->setPhoneDomicile(null)
            ->setPhoneMobile(null)
            ->setAdr('')
            ->setComplement(null)
            ->setCp('')
            ->setCity('')
            ->setCreneau($creneau)
            ->setDay($day)
            ->setIsMobile($isMobile)
        ;
    }

    private function setToNullIfEmpty($item){
        return $item != "" ? $item : null;
    }
}