<?php


namespace App\Service;

use App\Entity\TicketDay;
use App\Entity\TicketProspect;
use App\Entity\TicketResponsable;
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
        $days = $this->em->getRepository(TicketDay::class)->findBy(array(), array('day' => 'ASC'));

        // $now = new DateTime(date('d-m-Y', strtotime('now')));
        // $tomorrow = new DateTime(date('d-m-Y', strtotime('+1 day')));

        $dayOpened = null;
        // foreach($days as $day){
        //     $d = $day->getDay();
        //     if($d <= $now || $d > $tomorrow){ // <= today OR > tomorrow -> close
        //         $day->setIsOpen(false);
        //     }

        //     if($d == $tomorrow){ // for tomorrow -> open
        //         $day->setIsOpen(true);
        //         $dayOpened = $day;
        //     }         
            
        //     $this->em->persist($day);
        // }
        date_default_timezone_set('Europe/Paris');

        $openAncien = new DateTime(date('d-m-Y\\TH:0:0', strtotime('16 July 2020 16:00:00')));
        $now = new DateTime();

        $findOne = false;
        foreach($days as $day){

            if($now >= $openAncien 
                && $day->getType() == TicketDay::TYPE_NOUVEAU
                && $now < $day->getDay()
                ){

                if($day->getRemaining() > 0){

                    if(!$findOne){
                        $findOne = true;
                        $day->setIsOpen(true);
                        $dayOpened = $day;
                    }else{
                        $day->setIsOpen(false);
                    }
                }else{
                    $day->setIsOpen(false);
                }
                
            }else{
                $day->setIsOpen(false);
            }
            
            $this->em->persist($day);
        }

        $this->em->flush();
        return $dayOpened;
    }
}
