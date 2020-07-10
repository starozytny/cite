<?php

namespace App\Controller\App;

use App\Entity\TicketCreneau;
use App\Entity\TicketDay;
use App\Entity\TicketProspect;
use App\Entity\TicketResponsable;
use App\Service\Mailer;
use App\Service\OpenDay;
use App\Service\Remaining;
use App\Service\ResponsableService;
use App\Service\TicketGenerator;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/reservation", name="app_booking_")
 */
class BookingController extends AbstractController
{
    private $remaining;
    private $responsableService;

    public function __construct(Remaining $remaining, ResponsableService $responsableService)
    {
        $this->remaining = $remaining;
        $this->responsableService = $responsableService;
    }

    /**
     * @Route("/", name="index")
     */
    public function index(OpenDay $openDay, SerializerInterface $serializer)
    {
        $openDay->open();
        $this->responsableService->deleteNonConfirmed();
        $em = $this->getDoctrine()->getManager();
        $days = $em->getRepository(TicketDay::class)->findAll();
        $day = $em->getRepository(TicketDay::class)->findOneBy(array('isOpen' => true));

        if(!$day){
            return $this->render('root/app/pages/booking/index.html.twig');
        }

        $days = $serializer->serialize($days, 'json', ['attributes' => ['typeString', 'day', 'isOpen', 'remaining']]);

        return $this->render('root/app/pages/booking/index.html.twig', [
            'day' => $day,
            'days' => $days
        ]);
    }

    /**
     * @Route("/tmp/book/{id}/add", options={"expose"=true}, name="tmp_book_add")
     */
    public function tmpBook(TicketDay $id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $day = $id;
        $creneaux = $em->getRepository(TicketCreneau::class)->findBy(array('ticketDay' => $id), array('horaire' => 'ASC'));

        $data = json_decode($request->getContent());
        $prospects = $data->prospects;
        $nbProspects = count($prospects);
        $responsable = $data->responsable;

        // Check place in each creneaux orderBy ASC horaire
        $i = 0; $len = count($creneaux);
        if($day->getRemaining() >= $nbProspects){ // suffisament de place pour le nombre de prospects
            
            foreach($creneaux as $creneau){

                $remaining = $creneau->getRemaining();
    
                if($remaining > 0){ // reste de la place
    
                    if($remaining >= $nbProspects){ // assez de place pour l'inscription
    
                        $retour = $this->createResponsableAndProspects($responsable, $prospects, $creneau, $day);
                        if(is_array($retour)){
                            return new JsonResponse(['code' => 2, 'duplicated' => $retour,
                            'message' => 'Un ou des personnes souhaitant s\'inscrire a déjà été enregistré par une autre réservation. <br/> <br/>
                                         S\'il s\'agit d\'une nouvelle tentative de réservation, veuillez patienter l\'expiration de la précèdente. <br/>
                                         Le temps d\'une sauvegarde de réservation est de 5 minutes à partir de cette page.']);
                        }
                        $horaire = date_format($creneau->getHoraire(), 'H\hi');
                        return new JsonResponse(['code' => 1, 'horaire' => $horaire, 'responsableId' => $retour, 
                            'message' => 'Horaire de passage : <b>' . $horaire . '</b> <br/><br/>
                                         Attention ! Si vous fermez ou rafraichissez cette page, vous devrez attendre 5 minutes pour une réitérer la demande.']);
                        
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
        }else{
            return new JsonResponse([
                'code' => 0,
                'message' => 'Il n\'y a plus assez de place. En validant la réservation, vous serez <b>en file d\'attente</b>.'
            ]);
        }
    }

    /**
     * @Route("/tmp/book/{id}/delete", options={"expose"=true}, name="tmp_book_delete")
     */
    public function deleteTmpBook(TicketDay $id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $data = json_decode($request->getContent());
        $responsableId = $data->responsable;

        if($responsableId != null){
            $responsable = $em->getRepository(TicketResponsable::class)->find($responsableId);
            if($responsable->getStatus() == TicketResponsable::ST_TMP){
                $this->responsableService->deleteResponsable($responsable);
            }
        }
        return new JsonResponse(['code' => 1]);
    }

    /**
     * @Route("/confirmed/book/{id}/add", options={"expose"=true}, name="confirmed_book_add")
     */
    public function book(TicketDay $id, TicketGenerator $ticketGenerator, Mailer $mailer, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $data = json_decode($request->getContent());
        $responsableId = $data->responsable;

        $responsable = $em->getRepository(TicketResponsable::class)->find($responsableId);

        if(!$responsable){
            return new JsonResponse(['code' => 0, 'message' => 'Erreur, la réservation n\'a pas pu aboutir.']);
        }

        do{
            $ticket = $ticketGenerator->generate($responsable);
            $existe = $em->getRepository(TicketResponsable::class)->findOneBy(array('ticket' => $ticket));
        }while($existe);

        $responsable->setTicket($ticket);
        $responsable->setStatus(TicketResponsable::ST_CONFIRMED);

        $prospects = $responsable->getProspects();
        foreach ($prospects as $prospect) {
            $prospect->setStatus(TicketProspect::ST_CONFIRMED);
            $horaire = $prospect->getCreneau()->getHoraire();
            $em->persist($prospect);
        }

        // Send mail     
        $title = 'Réservation journée des ' . $id->getTypeString() . ' du ' . date_format($id->getDay(), 'd/m/Y') . '. - Cité de la musique';
        if($mailer->sendMail(
            $title, $title,
            'root/app/email/booking/index.html.twig',
            ['ticket' => $ticket, 'horaire' => $horaire, 'day' => $id->getDay()],
            $responsable->getEmail()
        ) != true){
            return new JsonResponse([ 'code' => 0, 'errors' => 'Erreur, le service d\'envoie de mail est indisponible.' ]);
        }

        $em->persist($responsable); $em->flush();
        return new JsonResponse(['code' => 1, 'ticket' => $ticket, 'message' => 'Réservation réussie. Un mail récapitulatif a été envoyé à l\'adresse
            du responsable : ' . $responsable->getEmail()]);
    }

    /**
     * Create Responsable and Prospects and check if At least one prospect is not exist else
     * decrease remaining creneau and day 
     */
    private function createResponsableAndProspects($resp, $prospects, TicketCreneau $creneau, TicketDay $day, $waiting=false)
    {
        $em = $this->getDoctrine()->getManager();
        $alreadyRegistered = [];

        $responsable = $this->responsableService->createResponsable($resp, $waiting);
        $em->persist($responsable);

        foreach($prospects as $item){
            $prospect = $this->createProspect($item, $creneau, $responsable);

            if($em->getRepository(TicketProspect::class)->findOneBy(array(
                'civility' => $item->civility,
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

        $this->remaining->decreaseRemaining($day, $creneau, count($prospects));

        $em->flush();
        return $responsable->getId();
    }

   

    /**
     * Create Ticket Prospect
     */
    private function createProspect($item, $creneau, $responsable)
    {
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
