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
        // Open day
        $openDay->open();
        // Delete user no confirme register
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
     * @Route("/tmp/book/{id}/start", options={"expose"=true}, name="tmp_book_start")
     */
    public function start(TicketDay $id)
    {
        $em = $this->getDoctrine()->getManager();

        $day = $id;
        $creneaux = $em->getRepository(TicketCreneau::class)->findBy(array('ticketDay' => $id), array('horaire' => 'ASC'));
    
        $i = 0; $len = count($creneaux);
        if($day->getRemaining() > 0 ){ // il reste des tickets
            
            foreach($creneaux as $creneau){
                $remaining = $creneau->getRemaining();
                if($remaining > 0){ // reste de la place dans ce creneau

                    $responsable = $this->responsableService->createTmpResponsable($creneau);
                    $this->remaining->decreaseRemaining($day, $creneau);

                    $em->persist($responsable); $em->flush();

                    return new JsonResponse(['code' => 1, 'creneauId' => $creneau->getId(), 'responsableId' => $responsable->getId()]);    

                }else{
                    if($i == $len - 1) {
                        return new JsonResponse([ 'code' => 0, 'message' => "il n\'y a plus de place."]);
                    }
                }
            }
        }else{
            return new JsonResponse([ 'code' => 0, 'message' => "il n\'y a plus de place."]);
        }
    }

    /**
     * @Route("/tmp/book/reset/timer/{responsableId}", options={"expose"=true}, name="reset_timer")
     */
    public function resetTimer(TicketResponsable $responsableId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $responsable = $responsableId;
        $responsable->setCreateAt(new DateTime());
        $em->persist($responsable); $em->flush();
    
        return new JsonResponse([ 'code' => 1 ]);
    }

    /**
     * @Route("/tmp/book/{id}/duplicate", options={"expose"=true}, name="tmp_book_duplicate")
     */
    public function duplicateProspect(TicketDay $id, Request $request)
    {
        $day = $id;
        $data = json_decode($request->getContent());
        $prospects = $data->prospects;

        $alreadyRegistered = $this->alreadyRegistered($prospects, $day->getType());
        if(count($alreadyRegistered) != 0){
            return new JsonResponse(['code' => 2, 'duplicated' => $alreadyRegistered]);
        }

        return new JsonResponse(['code' => 1]);
    }

    /**
     * @Route("/tmp/book/{id}/add", options={"expose"=true}, name="tmp_book_add")
     */
    public function tmpBook(TicketDay $id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $day = $id;
        $data = json_decode($request->getContent());
        $prospects = $data->prospects;
        $responsableId = $data->responsableId;
        $responsable = $data->responsable;
        $creneauId = $data->creneauId;

        $creneau = $em->getRepository(TicketCreneau::class)->find($creneauId);
        $res = $this->createResponsableAndProspects($responsableId, $responsable, $prospects, $creneau, $day);
        if($res){
            $horaire = date_format($creneau->getHoraire(), 'H\hi');
            return new JsonResponse(['code' => 1, 'horaire' => $horaire, 'responsableId' => $responsableId, 
                'message' => 'Horaire de passage : <b>' . $horaire . '</b>'
            ]);
        }else{
            return new JsonResponse([
                'code' => 0,
                'message' => 'Une erreur est survenue pendant votre inscription. Veuillez recommencer.'
            ]);
        }
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

        if(!$responsable->getIsWaiting()){
            $prospects = $responsable->getProspects();
            foreach ($prospects as $prospect) {
                $prospect->setStatus(TicketProspect::ST_CONFIRMED);
                $horaire = $prospect->getCreneau()->getHoraire();
                $em->persist($prospect);
            }

            $title = 'Réservation journée des ' . $id->getTypeString() . ' du ' . date_format($id->getDay(), 'd/m/Y') . '. - Cité de la musique';
            $html = 'root/app/email/booking/index.html.twig';
            $params =  ['ticket' => $ticket, 'horaire' => $horaire, 'day' => $id->getDay()];
            $file = $this->getParameter('barcode_directory') . '/pdf/' . $ticket . '-ticket.pdf';
        }else{
            $title = '[FILE ATTENTE] - Réservation journée des ' . $id->getTypeString() . ' du ' . date_format($id->getDay(), 'd/m/Y') . '. - Cité de la musique';
            $html = 'root/app/email/booking/index.html.twig';
            $params =  ['day' => $id->getDay()];
            $file = null;
        }      

        // Send mail     
        if($mailer->sendMail( $title, $title, $html, $params, $responsable->getEmail(), $file ) != true){
            return new JsonResponse([ 'code' => 0, 'errors' => 'Erreur, le service d\'envoie de mail est indisponible.' ]);
        }

        $em->persist($responsable); $em->flush();
        if(!$responsable->getIsWaiting()){
            return new JsonResponse(['code' => 1, 'ticket' => $ticket, 'message' => 'Réservation réussie. Un mail récapitulatif a été envoyé à l\'adresse
            du responsable : ' . $responsable->getEmail()]);
        }else{
            return new JsonResponse(['code' => 0, 'message' => 'Réservation en file d\'attente. Un mail récapitulatif a été envoyé à l\'adresse
            du responsable : ' . $responsable->getEmail()]);
        }
       
    }

    /**
     * Create Responsable and Prospects and check if At least one prospect is not exist else
     * decrease remaining creneau and day 
     */
    private function createResponsableAndProspects($responsableId, $resp, $prospects, ?TicketCreneau $creneau, TicketDay $day, $waiting=false)
    {
        $em = $this->getDoctrine()->getManager();

        $responsable = $this->responsableService->updateResponsable($responsableId, $resp, $waiting);
        if($responsable == false){
            return false;
        }
        $em->persist($responsable);

        foreach($prospects as $item){
            $prospect = $this->createProspect($item, $creneau, $responsable, $waiting);
            $em->persist($prospect);
        }     

        $em->flush();
        return $responsable->getId();
    }

    public function alreadyRegistered($prospects, $dayType)
    {
        $em = $this->getDoctrine()->getManager();
        $alreadyRegistered = [];

        if($dayType == TicketDay::TYPE_NOUVEAU){

            foreach($prospects as $item){
                if($em->getRepository(TicketProspect::class)->findOneBy(array(
                    'civility' => $item->civility,
                    'firstname' => $item->firstname,
                    'lastname' => $item->lastname,
                    'email' => $item->email,
                    'birthday' => new DateTime($item->birthday)
                ))){
                    array_push($alreadyRegistered, $item);
                } 
            }
           
        }else{

            foreach($prospects as $item){
                if($em->getRepository(TicketProspect::class)->findOneBy(array( 'numAdh' => $item->numAdh ))){
                    array_push($alreadyRegistered, $item);
                } 
            }

        }

        return $alreadyRegistered;
    }
   

    /**
     * Create Ticket Prospect
     */
    private function createProspect($item, $creneau, $responsable, $waiting=false)
    {
        $pro = (new TicketProspect())
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
        if($waiting){
            $pro->setStatus(TicketProspect::ST_WAITING);
        }
        return $pro;
    }

    private function setToNullIfEmpty($item){
        return $item != "" ? $item : null;
    }
}
