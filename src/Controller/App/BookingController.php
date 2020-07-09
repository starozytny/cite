<?php

namespace App\Controller\App;

use App\Entity\TicketCreneau;
use App\Entity\TicketDay;
use App\Entity\TicketProspect;
use App\Entity\TicketResponsable;
use App\Service\OpenDay;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reservation", name="app_booking_")
 */
class BookingController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(OpenDay $openDay)
    {
        $openDay->open();
        $em = $this->getDoctrine()->getManager();
        $day = $em->getRepository(TicketDay::class)->findOneBy(array('isOpen' => true));

        if(!$day){
            return $this->render('root/app/pageS/booking/index.html.twig');
        }

        return $this->render('root/app/pageS/booking/index.html.twig', [
            'day' => $day
        ]);
    }

    /**
     * @Route("/tmp/book/{id}", options={"expose"=true}, name="tmp_book")
     */
    public function tmpBook(TicketDay $id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $creneaux = $em->getRepository(TicketCreneau::class)->findBy(array('ticketDay' => $id), array('horaire' => 'ASC'));

        $data = json_decode($request->getContent());
        $prospects = $data->prospects;
        $nbProspects = count($prospects);
        $responsable = $data->responsable;

        // Check place in each creneaux orderBy ASC horaire
        $i = 0; $len = count($creneaux);
        foreach($creneaux as $creneau){

            $remaining = $creneau->getRemaining();

            if($remaining > 0){ // reste de la place

                if($remaining >= $nbProspects){ // assez de place pour l'inscription

                    $retour = $this->createResponsableAndProspects($responsable, $prospects, $creneau);
                    if(is_array($retour)){
                        return new JsonResponse(['code' => 2, 'message' => 'Un ou des personnes à inscrire ont déjà été enregistré.', 'duplicated' => $retour]);
                    }
                    return new JsonResponse(['code' => 1, 'message' => 'Horaire de passage : ' . date_format($creneau->getHoraire(), 'H\hi')]);
                    
                }else{ // pas assez de place pour l'inscription
                    // test le suivant sauf si last creneau
                    if($i == $len - 1) { 
                        return new JsonResponse([
                            'code' => 0,
                            'message' => 'Reste de la place mais pas assez pour le nombre de prospects -> file attente'
                        ]);
                    }
                }

            }else{ // pas de place 
                // test le suivant sauf si last creneau
                if($i == $len - 1) {
                    return new JsonResponse([
                        'code' => 0,
                        'message' => 'Plus de place dispo sur tous les créneaux -> file attente'
                    ]);
                }
            }
        }
        // persist & flush data
        // (set un timer pour supprimer l'inscription)
        // ------- [sinon]
        // message informatif de file d'attente


        return new JsonResponse([
            'code' => 1
        ]);
    }

    private function createResponsableAndProspects($resp, $prospects, TicketCreneau $creneau, $waiting=false)
    {
        $em = $this->getDoctrine()->getManager();
        $alreadyRegistered = [];

        $responsable = $this->createResponsable($resp, $waiting);
        $em->persist($responsable);

        foreach($prospects as $item){
            $prospect = $this->createProspect($item, $creneau, $responsable);

            if($em->getRepository(TicketProspect::class)->findOneBy(array(
                'firstname' => $item->firstname,
                'lastname' => $item->lastname,
                'email' => $item->email,
                'birthday' => new DateTime($item->birthday)
            ))){
                array_push($alreadyRegistered, $item);
            }else{
                $em->persist($prospect);
            }            
        }

        if(count($alreadyRegistered) != 0){
            return $alreadyRegistered;
        }

        $em->flush();
        return true;
    }

    private function createResponsable($resp, $waiting){
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

    private function createProspect($item, $creneau, $responsable){
        return (new TicketProspect())
            ->setFirstname($item->firstname)
            ->setLastname($item->lastname)
            ->setCivility($item->civility)
            ->setEmail($item->email)
            ->setBirthday(new DateTime($item->birthday))
            ->setPhoneDomicile($this->setToNullIfEmpty($item->phoneDomicile))
            ->setPhoneMobile($this->setToNullIfEmpty($item->phoneMobile))
            ->setAdr($item->adr)
            ->setCp($item->cp)
            ->setCity($item->city)
            ->setNumAdh($this->setToNullIfEmpty($item->numAdh))
            ->setResponsable($responsable)
            ->setCreneau($creneau)
        ;
    }

    private function setToNullIfEmpty($item){
        return $item != "" ? $item : null;
    }
}
