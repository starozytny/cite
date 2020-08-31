<?php


namespace App\Service;

use App\Entity\TicketDay;
use App\Entity\TicketFermeture;
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

    public function open()
    {
        date_default_timezone_set('Europe/Paris');
        $dayOpened = null;
        $days = $this->em->getRepository(TicketDay::class)->findBy(array(), array('day' => 'ASC'));

        $ancien = $this->em->getRepository(TicketOuverture::class)->findOneBy(array('type' => TicketOuverture::TYPE_ANCIEN));
        $fermeAncien = $this->em->getRepository(TicketFermeture::class)->findOneBy(array('type' => TicketOuverture::TYPE_ANCIEN));
        $nouveau = $this->em->getRepository(TicketOuverture::class)->findOneBy(array('type' => TicketOuverture::TYPE_NOUVEAU));
        $openAncien = $ancien->getOpen();
        $closeAncien = $fermeAncien->getClose();
        $openNouveau = $nouveau->getOpen();
        
        $now = new DateTime();
        // ancien est la date la plus petit d'ouverture
        if($openAncien < $openNouveau){ 
            // mais si la date des nouveaux est inférieur à aujourdhui (donc entre ancien et aujourd'hui),
            // nouveau = ouverture
            $ouverture = ($openNouveau < $now) ? $openNouveau : $openAncien; 
            $typeOuverture = ($openNouveau < $now) ? TicketOuverture::TYPE_NOUVEAU : TicketOuverture::TYPE_ANCIEN;
        }else{
            $ouverture = ($openAncien < $now) ? $openAncien : $openNouveau; 
            $typeOuverture = ($openAncien < $now) ? TicketOuverture::TYPE_ANCIEN : TicketOuverture::TYPE_NOUVEAU;
        }

        dump($closeAncien);

        $findOne = false;
        foreach($days as $day){

            if($now >= $ouverture 
                && $day->getType() == $typeOuverture
                && $now < $day->getDay()
                ){ //now est supérieur ou égale à la date/heure d'ouverture + le type demandé et le jour d'inscription parcouru est inférieur a la date de now

                if($typeOuverture == 0 && $now > $closeAncien){
                    $day->setIsOpen(false);
                }else{
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
