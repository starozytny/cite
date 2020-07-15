<?php

namespace App\Controller\App;

use App\Entity\TicketDay;
use App\Entity\TicketResponsable;
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
     * @Route("/responsable/{id}-{ticket}-{ticketDay}", name="index")
     */
    public function getTicket(TicketResponsable $id, $ticket, TicketDay $ticketDay)
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

        $file = $this->getParameter('barcode_directory') . '/' . $responsable->getId() . '-barcode.png';
        if(!file_exists($file)){
            return new Response(0);
        }

        if($responsable->getTicket() == $ticket){
            $mpdf = new Mpdf();

            $img = file_get_contents($file);
            $data = base64_encode($img);

            dump($data);

            $mpdf->SetTitle('Ticket cité de la musique - ' . $responsable->getFirstname() . ' ' . $responsable->getLastname());
            $stylesheet = file_get_contents($this->getParameter('kernel.project_dir') . '/public/pdf/css/bootstrap.min.css');
            $stylesheet2 = file_get_contents($this->getParameter('kernel.project_dir') . '/public/pdf/css/custom-pdf.css');
            $mpdf->WriteHTML($stylesheet,HTMLParserMode::HEADER_CSS);
            $mpdf->WriteHTML($stylesheet2,HTMLParserMode::HEADER_CSS);
            $mpdf->SetProtection(array(
                'print'
            ),'', 'Pf3zGgig5hy5');

            $mpdf->WriteHTML(
                $this->renderView('root/app/pdf/ticket.html.twig', ['day' => $day, 'creneau' => $creneau, 'responsable' => $responsable, 'prospects' => $prospects, 'image' => $data]),
                HTMLParserMode::HTML_BODY
            );

            $r = $mpdf->Output('ticket-'.$responsable->getId().'.pdf', Destination::INLINE);

            return new Response(1);

        }

    }
}
