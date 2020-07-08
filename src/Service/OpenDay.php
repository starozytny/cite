<?php


namespace App\Service;

use App\Entity\TicketDay;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class OpenDay
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function open()
    {
        $days = $this->em->getRepository(TicketDay::class)->findAll();

        $now = new DateTime(date('d-m-Y', strtotime('now')));
        $tomorrow = new DateTime(date('d-m-Y', strtotime('+1 day')));

        foreach($days as $day){
            $d = $day->getDay();
            if($d <= $now || $d > $tomorrow){ // <= today OR > tomorrow -> close
                $day->setIsOpen(false);
            }

            if($d == $tomorrow){ // for tomorrow -> open
                $day->setIsOpen(true);
            }         
            
            $this->em->persist($day);
        }

        $this->em->flush();
    }
}
