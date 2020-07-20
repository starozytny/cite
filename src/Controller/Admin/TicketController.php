<?php

namespace App\Controller\Admin;

use App\Entity\TicketCreneau;
use App\Entity\TicketDay;
use App\Entity\TicketHistory;
use App\Entity\TicketProspect;
use App\Entity\TicketResponsable;
use App\Service\Export;
use App\Service\OpenDay;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
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
            'id', 'firstname', 'lastname', 'civility', 'email', 'birthday', 'age', 'phoneDomicile', 'phoneMobile', 'adr', 'cp', 'city',
            'numAdh', 'status', 'statusString', 'responsable' => ['id'], 'creneau' => ['id', 'horaireString']
        ]]);

        return $this->render('root/admin/pages/ticket/show.html.twig', [
            'day' => $ticketDay,
            'slots' => $slots,
            'prospects' => $prospects
        ]);
    }

    /**
    * @Route("/jour/{ticketDay}/historique", name="history")
    */
    public function history(TicketDay $ticketDay)
    {
        $em = $this->getDoctrine()->getManager();
        $histories = $em->getRepository(TicketHistory::class)->findBy(array('day' => $ticketDay), array('createAt' => 'ASC'));

        return $this->render('root/admin/pages/ticket/history.html.twig', [
            'day' => $ticketDay,
            'histories' => $histories
        ]);
    }

    /**
    * @Route("/jour/{ticketDay}/editer", name="edit")
    */
    public function edit(SerializerInterface $serializer, TicketDay $ticketDay)
    {
        $em = $this->getDoctrine()->getManager();
        $slots = $em->getRepository(TicketCreneau::class)->findBy(array('ticketDay' => $ticketDay), array('horaire' => 'ASC'));
        $slots = $serializer->serialize($slots, 'json', ['attributes' => ['id', 'horaire', 'horaireString', 'max', 'remaining']]);

        return $this->render('root/admin/pages/ticket/edit.html.twig', [
            'day' => $ticketDay,
            'slots' => $slots
        ]);
    }

    /**
    * @Route("/jour/{ticketDay}/slot/editer/update", options={"expose"=true}, name="slot_update")
    */
    public function updateSlot(TicketDay $ticketDay, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent());
        $slotId = $data->slotId;
        $max = intval($data->max);

        $slot = $em->getRepository(TicketCreneau::class)->find($slotId);

        if(!$slot){
            return new JsonResponse(['code' => 0, 'message' => 'Erreur, ce créneau n\'existe pas.']);
        }

        $min = $slot->getMax() - $slot->getRemaining();
        if($max < $min){
            return new JsonResponse(['code' => 0, 'message' => 'La nouvelle valeur min acceptée est ' . $min . '. Veuillez rafraichir la page.']);
        }
        $remaining = $max - $min;
        $slot->setMax($max);
        $slot->setRemaining($remaining);
        $em->persist($slot);
        $em->flush();

        return new JsonResponse(['code' => 1, 'remaining' => $remaining]);
    }

    /**
    * @Route("/jour/{ticketDay}/slot/add", options={"expose"=true}, name="slot_add")
    */
    public function addSlot(TicketDay $ticketDay, Request $request, SerializerInterface $serializer)
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent());
        $hours = intval($data->hours);
        $minutes = intval($data->minutes);
        $max = intval($data->max);

        if($max < 0){
            return new JsonResponse(['code' => 0, 'message' => 'La valeur max doit être supérieur à 0.']);
        }

        $slots = $em->getRepository(TicketCreneau::class)->findBy(array('ticketDay' => $ticketDay));        
        foreach ($slots as $slot){
            $h = intval(date_format($slot->getHoraire(), 'H'));
            $m = intval(date_format($slot->getHoraire(), 'i'));
            if($h == $hours && $m == $minutes){
                return new JsonResponse(['code' => 0, 'message' => 'Cet horaire existe déjà.']);
            }
        }
        $minutes = $minutes == 0 ? '00' : $minutes;
        $horaire = $hours . ':' . $minutes;

        $new = (new TicketCreneau())
            ->setHoraire(date_create_from_format('H:i', $horaire))
            ->setMax($max)
            ->setRemaining($max)
            ->setTicketDay($ticketDay)
        ;

        $ticketDay->setMax($ticketDay->getMax() + $max);
        $ticketDay->setRemaining($ticketDay->getRemaining() + $max);

        $em->persist($new); $em->persist($ticketDay);
        $em->flush();

        $slots = $em->getRepository(TicketCreneau::class)->findBy(array('ticketDay' => $ticketDay), array('horaire' => 'ASC'));
        $slots = $serializer->serialize($slots, 'json', ['attributes' => ['id', 'horaire', 'horaireString', 'max', 'remaining']]);

        return new JsonResponse(['code' => 1, 'slots' => $slots]);
    }

    /**
    * @Route("/jour/{ticketDay}/delete/{slot}", options={"expose"=true}, name="slot_delete")
    */
    public function deleteSlot(TicketDay $ticketDay, TicketCreneau $slot)
    {
        $em = $this->getDoctrine()->getManager();
        $horaire = $slot->getHoraireString();

        if(!$slot){
            return new JsonResponse(['code' => 0, 'message' => 'Erreur, ce créneau n\'existe pas.']);
        }

        if($slot->getRemaining() < $slot->getMax()){
            return new JsonResponse(['code' => 0, 'message' => 'Une personne vient de s\'inscrire. Vous ne pouvez pas supprimer ce créneau. Veuillez rafraichir la page.']);
        }

        $ticketDay->setMax($ticketDay->getMax() - $slot->getMax());
        $ticketDay->setRemaining($ticketDay->getRemaining() - $slot->getRemaining());

        $em->persist($ticketDay); $em->remove($slot);
        $em->flush();

        return new JsonResponse(['code' => 1, 'message' => 'Le créneau ' . $horaire . ' a été définitivement supprimé.']);
    }

    /**
    * @Route("/jour/{ticketDay}/export", name="export")
    */
    public function export(TicketDay $ticketDay, Export $export)
    {
        $em = $this->getDoctrine()->getManager();
        $responsables = $em->getRepository(TicketResponsable::class)->findBy(array('day' => $ticketDay));
        $data = array();

        foreach ($responsables as $responsable) {
            $prospects = $responsable->getProspects();
            if(count($prospects) > 0 && $responsable->getStatus() != TicketResponsable::ST_TMP){

                $commentary = "[" . date_format($responsable->getCreneau()->getHoraire(), 'H\hi') . "] - [". count($prospects) ."] - ";
                $i=0;
                foreach ($prospects as $prospect){
                    $i++;
                    $commentary .= $prospect->getCivility() . ' ' . $prospect->getFirstName() . " " . $prospect->getLastname();
                    $commentary .= count($prospects) == $i ? "" : " / ";
                }

                $tmp = array(
                    $responsable->getTicket(),
                    "Tarif Gratuit",
                    0,
                    $responsable->getLastname(),
                    $responsable->getFirstname(),
                    $responsable->getEmail(),
                    date_format($responsable->getCreneau()->getHoraire(), 'H\hi'),
                    $commentary
                );
                if(!in_array($tmp, $data)){
                    array_push($data, $tmp);
                }
            }
            
        }

        $fileName = 'liste-' . $ticketDay->getId() . '.csv';

        $header = array(array('CODE-BARRE', 'NOM DU TARIF', 'PRIX', 'NOM', 'PRENOM', 'E-MAIL', 'SOCIETE', 'COMMENTAIRE'));
        $json = $export->createFile('csv', 'Liste des utilisateurs du ' . $ticketDay->getId(), $fileName , $header, $data, 8, null);
        
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="liste-' . $ticketDay->getId() .'.csv"');
        return new BinaryFileResponse($this->getParameter('export_directory'). '/' . $fileName);
    }
}
