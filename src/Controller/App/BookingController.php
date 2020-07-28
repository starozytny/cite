<?php

namespace App\Controller\App;

use App\Entity\Cite\CiAdherent;
use App\Entity\TicketCreneau;
use App\Entity\TicketDay;
use App\Entity\TicketHistory;
use App\Entity\TicketProspect;
use App\Entity\TicketResponsable;
use App\Service\History;
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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/reservation", name="app_booking_")
 */
class BookingController extends AbstractController
{
    private $remaining;
    private $responsableService;
    private $history;

    public function __construct(Remaining $remaining, ResponsableService $responsableService, History $history)
    {
        $this->remaining = $remaining;
        $this->responsableService = $responsableService;
        $this->history = $history;
    }

    /**
     * @Route("/", name="index")
     */
    public function index(OpenDay $openDay, SerializerInterface $serializer)
    {
        // Open day
        // Delete user no confirme register
        $this->responsableService->deleteNonConfirmed();
        $em = $this->getDoctrine()->getManager();
        $days = $em->getRepository(TicketDay::class)->findAll();
        $day = $openDay->open();

        if(!$day){
            return $this->render('root/app/pages/booking/index.html.twig');
        }

        $days = $serializer->serialize($days, 'json', ['attributes' => ['typeString', 'day', 'isOpen', 'remaining', 'fullDateString']]);

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

                    $responsable = $this->responsableService->createTmpResponsable($creneau, $day);
                    $history = $this->history->createHistory($creneau, $day);
                    $this->remaining->decreaseRemaining($day, $creneau);

                    $em->persist($responsable); $em->persist($history); $em->flush();

                    return new JsonResponse(['code' => 1, 'creneauId' => $creneau->getId(), 'responsableId' => $responsable->getId(), 'historyId' => $history->getId()]);    

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
        $em = $this->getDoctrine()->getManager();

        $data = json_decode($request->getContent());
        $prospects = $data->prospects;

        $alreadyRegistered = $this->alreadyRegistered($prospects, $data->responsable);
        if(count($alreadyRegistered) != 0){
            return new JsonResponse(['code' => 2, 'duplicated' => $alreadyRegistered]);
        }
        $this->history->updateFamille($data->historyId, count($prospects));

        $creneau = $em->getRepository(TicketCreneau::class)->find($data->creneauId);
        $horaire = date_format($creneau->getHoraire(), 'H\hi');
        $this->history->updateResp($data->historyId, $data->responsable);
        return new JsonResponse(['code' => 1, 'horaire' => $horaire, 'message' => 'Horaire de passage : <b>' . $horaire . '</b>' ]);
    }

    /**
     * @Route("/confirmed/book/{id}/add", options={"expose"=true}, name="confirmed_book_add")
     */
    public function book(TicketDay $id, TicketGenerator $ticketGenerator, Mailer $mailer, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $data = json_decode($request->getContent());
        $responsableId = $data->responsableId;
        $responsableData = $data->responsable;
        $prospects = $data->prospects;

        $creneau = $em->getRepository(TicketCreneau::class)->find($data->creneauId);
        $responsable = $this->createResponsableAndProspects($responsableId, $responsableData, $prospects, $creneau, $id);
        if($responsable != false){
            $prospects = $em->getRepository(TicketProspect::class)->findBy(array('responsable' => $responsableId));
            do{
                $ticket = $ticketGenerator->generate($responsable, $prospects);
                $existe = $em->getRepository(TicketResponsable::class)->findOneBy(array('ticket' => $ticket));
            }while($existe);
    
            $responsable->setTicket($ticket);
            $responsable->setStatus(TicketResponsable::ST_CONFIRMED);
            $horaireString = $responsable->getCreneau()->getHoraireString();

            $title = 'Reservation journee des ' . $id->getTypeString() . ' du ' . date_format($id->getDay(), 'd/m/Y') . '. - Cite de la musique';
            $html = 'root/app/email/booking/index.html.twig';
            $file = $this->getParameter('barcode_directory') . '/pdf/' . $ticket . '-ticket.pdf';
            $img = file_get_contents($this->getParameter('barcode_directory') . '/' . $responsable->getId() . '-barcode.jpg');
            $barcode = base64_encode($img);
            $params =  ['ticket' => $ticket, 'barcode' => $barcode, 'horaire' => $horaireString, 'day' => $id, 'responsable' => $responsable, 'prospects' => $prospects];
            $print = $this->generateUrl('app_ticket_get', ['id' => $responsable->getId(), 'ticket' => $ticket, 'ticketDay' => $id->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
    
            // Send mail     
            if($mailer->sendMail( $title, $title, $html, $params, $responsable->getEmail(), $file, $responsable ) != true){
                return new JsonResponse([ 'code' => 0, 'errors' => 'Erreur, le service d\'envoie de mail est indisponible.' ]);
            }
    
            $this->history->updateTicket($data->historyId);
            $em->persist($responsable); $em->flush();
            return new JsonResponse(['code' => 1, 'ticket' => $ticket, 'barcode' => $barcode, 'print' => $print, 'message' => $responsable->getEmail()]);
        }else{
            return new JsonResponse(['code' => 0, 'message' => 'Erreur, la réservation n\'a pas pu aboutir.']);
        }
    }

    /**
     * @Route("/tmp/book/{id}/cancel", options={"expose"=true}, name="tmp_book_cancel")
     */
    public function cancel(TicketDay $id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $data = json_decode($request->getContent());
        $responsableId = $data->responsableId;

        $responsable = $em->getRepository(TicketResponsable::class)->find($responsableId);
        $this->responsableService->deleteResponsable($responsable);
        $em->flush();
        
        $url = $this->generateUrl('app_booking_index', [], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse(['code' => 1, 'url' => $url]);
    }

        /**
     * @Route("/tmp/prospect/preset", options={"expose"=true}, name="tmp_prospect_preset")
     */
    public function preset(Request $request, SerializerInterface $serializer)
    {
        $em = $this->getDoctrine()->getManager();

        $data = json_decode($request->getContent());
        $numAdh = $data->numAdh;
        $adh = $em->getRepository(CiAdherent::class)->findOneBy(array('numAdh' => $numAdh));

        $prospect = $serializer->serialize($adh, 'json', ['attributes' => [
            'id', 'firstname', 'lastname', 'civility', 'email', 'birthday', 'birthdayJavascript', 'phoneDomicile', 'phoneMobile', 'adr', 'cp', 'city'
        ]]);
        
        if($adh){
            return new JsonResponse(['code' => 1, 'infos' => $prospect]);
        }

        return new JsonResponse(['code' => 0]);
    }

    /**
     * Create Responsable and Prospects and check if At least one prospect is not exist else
     * decrease remaining creneau and day 
     */
    private function createResponsableAndProspects($responsableId, $resp, $prospects, ?TicketCreneau $creneau, $day, $waiting=false)
    {
        $em = $this->getDoctrine()->getManager();

        $responsable = $this->responsableService->updateResponsable($responsableId, $resp, $waiting);
        if($responsable == false){
            return false;
        }
        $em->persist($responsable);

        foreach($prospects as $item){
            $prospect = $this->createProspect($item, $day, $creneau, $responsable, $waiting);
            $em->persist($prospect);
        }     

        $em->flush();
        return $responsable;
    }

    public function alreadyRegistered($prospects, $responsable)
    {
        $em = $this->getDoctrine()->getManager();
        $alreadyRegistered = [];

        foreach($prospects as $item){
                
            $birthday = date("Y-m-d", strtotime(str_replace('/', '-', $item->birthday)));
            $numAdh = $item->numAdh == "" ? null : $item->numAdh;

            $existe = $em->getRepository(TicketProspect::class)->findOneBy(array(
                'civility' => $item->civility,
                'firstname' => $item->firstname,
                'lastname' => $item->lastname,
                'email' => $item->email != "" ? $item->email : $responsable->email,
                'birthday' => new DateTime($birthday),
                'numAdh' => $numAdh
            ));
            if($existe){
                array_push($alreadyRegistered, $item);
            }
        }

       

        return $alreadyRegistered;
    }
   

    /**
     * Create Ticket Prospect
     */
    private function createProspect($item, $day, $creneau, $responsable, $waiting=false)
    {
        $em = $this->getDoctrine()->getManager();
        $birthday = date("Y-m-d", strtotime(str_replace('/', '-', $item->birthday)));

        $phoneMobile = $item->phoneMobile != "" ? $this->setToNullIfEmpty($item->phoneMobile) : $responsable->getPhoneMobile();
        $email = $item->email != "" ? $item->email : $responsable->getEmail();

        $adh = null;
        if($item->numAdh != ""){
            $adh = $em->getRepository(CiAdherent::class)->findOneBy(array('numAdh' => $item->numAdh));
        }

        $pro = (new TicketProspect())
            ->setFirstname($item->firstname)
            ->setLastname($item->lastname)
            ->setCivility($item->civility)
            ->setEmail($email)
            ->setBirthday(new DateTime($birthday))
            ->setPhoneDomicile(null)
            ->setPhoneMobile($phoneMobile)
            ->setAdr($responsable->getAdr())
            ->setCp($responsable->getCp())
            ->setCity($responsable->getCity())
            ->setNumAdh($this->setToNullIfEmpty($item->numAdh))
            ->setResponsable($responsable)
            ->setCreneau($creneau)
            ->setDay($day)
            ->setStatus(TicketProspect::ST_CONFIRMED)
            ->setAdherent($adh)
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
