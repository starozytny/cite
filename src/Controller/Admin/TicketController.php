<?php

namespace App\Controller\Admin;

use App\Entity\TicketDay;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/ticket", name="admin_ticket_")
 */
class TicketController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $days = $em->getRepository(TicketDay::class)->findBy(array(), array('day' => 'ASC'));

        return $this->render('root/admin/pages/ticket/index.html.twig', [
            'days' => $days
        ]);
    }
}
