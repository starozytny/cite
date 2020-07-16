<?php


namespace App\Service;

use App\Entity\TicketCreneau;
use App\Entity\TicketDay;
use Doctrine\ORM\EntityManagerInterface;

class Remaining
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

   /**
     * Increase remaining number for Ticket Creneau and Ticket Day
     */
    public function increaseRemaining(TicketDay $day, TicketCreneau $creneau, $nb = 1)
    {
        $creneau->setRemaining($creneau->getRemaining() + $nb);
        $day->setRemaining($day->getRemaining() + $nb);

        $this->em->persist($day); $this->em->persist($creneau); 
    }

    /**
     * Reduce remaining number for Ticket Creneau and Ticket Day
     */
    public function decreaseRemaining(TicketDay $day, TicketCreneau $creneau, $nb = 1)
    {
        $creneau->setRemaining($creneau->getRemaining() - $nb);
        $day->setRemaining($day->getRemaining() - $nb);

        $this->em->persist($day); $this->em->persist($creneau); 
    }
}