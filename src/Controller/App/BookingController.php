<?php

namespace App\Controller\App;

use App\Entity\TicketCreneau;
use App\Entity\TicketDay;
use App\Entity\TicketProspect;
use App\Entity\TicketResponsable;
use App\Service\OpenDay;
use App\Service\Remaining;
use App\Service\ResponsableService;
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
            return $this->render('root/app/pageS/booking/index.html.twig');
        }

        $days = $serializer->serialize($days, 'json', ['attributes' => ['typeString', 'day', 'isOpen']]);

        return $this->render('root/app/pageS/booking/index.html.twig', [
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
                            'message' => 'Un ou des personnes à inscrire ont déjà été enregistré. <br/> 
                                         S\'il s\'agit d\'une nouvelle tentative, veuillez patienter l\'expiration de la précèdente réservation. <br/>
                                         Le temps de sauvegarde est de 5 minutes à partir de cette page.']);
                        }
                        return new JsonResponse(['code' => 1, 'responsableId' => $retour, 
                            'message' => 'Horaire de passage : <b>' . date_format($creneau->getHoraire(), 'H\hi' . '</b> <br/>
                                         Attention ! Si vous fermez ou rafraichissez cette page, vous devrez attendre 5 minutes pour une réitérer la demande.')]);
                        
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
                'message' => 'Plus de place pour cette journée.'
            ]);
        }
        
        // persist & flush data
        // (set un timer pour supprimer l'inscription)
        // ------- [sinon]
        // message informatif de file d'attente


        return new JsonResponse([
            'code' => 1
        ]);
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
            $this->responsableService->deleteResponsable($responsable);
        }
        return new JsonResponse(['code' => 1]);
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
