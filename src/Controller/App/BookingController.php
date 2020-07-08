<?php

namespace App\Controller\App;

use App\Entity\TicketCreneau;
use App\Entity\TicketDay;
use App\Service\OpenDay;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function tmpBook(TicketDay $id)
    {
        $em = $this->getDoctrine()->getManager();
        $creneaux = $em->getRepository(TicketCreneau::class)->findBy(array('ticketDay' => $id), array('horaire' => 'ASC'));
        // check s'il y a de la place
        // ------- [si] -> OUI
        foreach($creneaux as $creneau){
            dump($creneau);
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
