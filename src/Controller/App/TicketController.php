<?php

namespace App\Controller\App;

use App\Entity\TicketDay;
use App\Entity\TicketResponsable;
use App\Service\TicketGenerator;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Mpdf\HTMLParserMode;

/**
 * @Route("/ticket", name="app_ticket_")
 */
class TicketController extends AbstractController
{

    /**
     * @Route("/responsable/{id}-{ticket}-{ticketDay}", options={"expose"=true}, name="get")
     */
    public function getTicket(TicketResponsable $id, $ticket, TicketDay $ticketDay, TicketGenerator $ticketGenerator)
    {
        $em = $this->getDoctrine()->getManager();

        $responsable = $id;
        $day = $ticketDay;
        if(!$responsable){
            return new Response(0);
        }

        $prospects = $responsable->getProspects();
        $prospect = $prospects[0];
        $creneau = $prospect->getCreneau();

        $file = $this->getParameter('barcode_directory') . '/' . $responsable->getId() . '-barcode.jpg';
        if(!file_exists($file)){
            return new Response(0);
        }

        if($responsable->getTicket() == $ticket){
            $mpdf = $ticketGenerator->createFileTicket($file, $responsable, $day, $creneau, $prospects, $responsable->getTicket());
            $r = $mpdf->Output('ticket-'.$responsable->getId().'.pdf', Destination::INLINE);

            return new Response(1);

        }

    }
}
