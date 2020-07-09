<?php

namespace App\Controller\App;

use App\Entity\TicketCreneau;
use App\Entity\TicketDay;
use App\Service\OpenDay;
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
     * @Route("/tmp/book/{id}/{nbProspects}", options={"expose"=true}, name="tmp_book")
     */
    public function tmpBook(TicketDay $id, $nbProspects, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $creneaux = $em->getRepository(TicketCreneau::class)->findBy(array('ticketDay' => $id), array('horaire' => 'ASC'));

        // Check place in each creneaux orderBy ASC horaire
        $i = 0; $len = count($creneaux);
        foreach($creneaux as $creneau){

            $remaining = $creneau->getRemaining();

            if($remaining > 0){ // reste de la place

                if($remaining >= $nbProspects){ // assez de place pour l'inscription

                    

                    return new JsonResponse([
                        'code' => 1,
                        'message' => 'Parfait'
                    ]);
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
}
