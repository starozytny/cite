<?php


namespace App\Service;

use App\Entity\TicketDay;
use App\Entity\TicketOuverture;
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

    // $now = new DateTime(date('d-m-Y', strtotime('now')));
        // $tomorrow = new DateTime(date('d-m-Y', strtotime('+1 day')));

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

    public function open()
    {
        date_default_timezone_set('Europe/Paris');
        $dayOpened = null;
        $days = $this->em->getRepository(TicketDay::class)->findBy(array(), array('day' => 'ASC'));

        $ancien = $this->em->getRepository(TicketOuverture::class)->findOneBy(array('type' => TicketOuverture::TYPE_ANCIEN));
        $nouveau = $this->em->getRepository(TicketOuverture::class)->findOneBy(array('type' => TicketOuverture::TYPE_NOUVEAU));
        $openAncien = $ancien->getOpen();
        $openNouveau = $nouveau->getOpen();
        
        $now = new DateTime();
        $ouverture = $openAncien < $openNouveau ? $openAncien : $openNouveau;
        $typeOuverture = $openAncien < $openNouveau ? TicketOuverture::TYPE_ANCIEN : TicketOuverture::TYPE_NOUVEAU;

        $findOne = false;
        foreach($days as $day){

            if($now >= $ouverture 
                && $day->getType() == $typeOuverture
                && $now < $day->getDay()
                ){ //now est supérieur ou égale à la date/heure d'ouverture + le type demandé et le jour d'inscription parcouru est inférieur a la date de now

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
