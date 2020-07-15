<?php

namespace App\Controller\Admin;

use App\Entity\TicketCreneau;
use App\Entity\TicketDay;
use App\Entity\TicketProspect;
use App\Form\TicketDayType;
use App\Service\OpenDay;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/admin/ticket", name="admin_ticket_")
 */
class TicketController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(OpenDay $openDay)
    {
        $em = $this->getDoctrine()->getManager();
        $days = $em->getRepository(TicketDay::class)->findBy(array(), array('day' => 'ASC'));

        $openDay->open();

        return $this->render('root/admin/pages/ticket/index.html.twig', [
            'days' => $days
        ]);
    }

    /**
    * @Route("/jour/{ticketDay}/details", name="show")
    */
    public function show(TicketDay $ticketDay, SerializerInterface $serializer)
    {
        $em = $this->getDoctrine()->getManager();
        $slots = $em->getRepository(TicketCreneau::class)->findBy(array('ticketDay' => $ticketDay), array('horaire' => 'ASC'));
        $prospects = $em->getRepository(TicketProspect::class)->findBy(array('creneau' => $slots));

        $slots = $serializer->serialize($slots, 'json', ['attributes' => ['id', 'horaire', 'max', 'remaining']]);
        $prospects = $serializer->serialize($prospects, 'json', ['attributes' => [
            'id', 'firstname', 'lastname', 'civility', 'email', 'birthday', 'phoneDomicile', 'phoneMobile', 'adr', 'cp', 'city',
            'numAdh', 'status', 'statusString', 'responsable' => ['id'], 'creneau' => ['id', 'horaireString']
        ]]);

        return $this->render('root/admin/pages/ticket/show.html.twig', [
            'day' => $ticketDay,
            'slots' => $slots,
            'prospects' => $prospects
        ]);
    }

    /**
    * @Route("/jour/{ticketDay}/editer", name="edit")
    */
    public function edit(Request $request, TicketDay $ticketDay)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(TicketDayType::class, $ticketDay);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $day = $form->getData();

            $em->persist($day);
            $em->flush();

            return $this->redirectToRoute('admin_ticket_index');
        }

        return $this->render('root/admin/pages/ticket/edit.html.twig', [
            'day' => $ticketDay,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/prospect/{id}/update/status", name="prospect_update_status")
     */
    public function changeStatus(TicketProspect $id)
    {
    }
}
