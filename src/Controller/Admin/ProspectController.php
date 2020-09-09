<?php

namespace App\Controller\Admin;

use App\Entity\TicketProspect;
use App\Service\Remaining;
use App\Service\ResponsableService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/admin/prospect", name="admin_prospect_")
 */
class ProspectController extends AbstractController
{
    /**
     * @Route("/eleve/{id}/update/status", options={"expose"=true}, name="update_status")
     */
    public function changeStatus(TicketProspect $id, SerializerInterface $serializer)
    {
        $em = $this->getDoctrine()->getManager();

        $prospect = $id;
        if(!$prospect){
            return new JsonResponse(['code' => 0]);
        }

        $existent = array();

        if($prospect->getStatus() == TicketProspect::ST_CONFIRMED){
            $prospect->setStatus(TicketProspect::ST_REGISTERED);
            $status = TicketProspect::ST_REGISTERED;
            $statusString = "Inscrit";
            $diff = 1;
            $existent = $this->haveExiste($prospect, $diff, 0);
        }else{
            $prospect->setStatus(TicketProspect::ST_CONFIRMED);
            $status = TicketProspect::ST_CONFIRMED;
            $statusString = "Attente";
            $diff = 0;
            $existent = $this->haveExiste($prospect, $diff, 1);
        }
        $em->persist($prospect);
        $em->flush();

        $prospect = $serializer->serialize($prospect, 'json', ['attributes' => [
            'id', 'firstname', 'lastname', 'civility', 'email', 'birthday', 'age', 'phoneDomicile', 'phoneMobile', 'adr', 'cp', 'city',
            'numAdh', 'status', 'statusString', 'isDiff',
            'responsable' => ['id', 'civility', 'firstname', 'lastname', 'email'],
            'day' => ['id', 'type', 'typeString']
        ]]);

        return new JsonResponse(['code' => 1, 'status' => $status, 'statusString' => $statusString, 'prospect' => $prospect, 'existent' => json_encode($existent), 'diff' => $diff]);
    }

    private function haveExiste($prospect, $diff, $contraireDiff)
    {
        $em = $this->getDoctrine()->getManager();
        $doublons = array();
        $existent = $em->getRepository(TicketProspect::class)->findBy(array( 
            'firstname' => $prospect->getFirstname(),
            'lastname' => $prospect->getLastname()
        ));

        $findOne = false;

        if($existent){
            foreach($existent as $existe){
                if($existe->getId() != $prospect->getId()){
                    if($existe->getBirthday()->format('Y') == $prospect->getBirthday()->format('Y')
                        && $existe->getBirthday()->format('D') == $prospect->getBirthday()->format('D')
                        && $existe->getBirthday()->format('M') == $prospect->getBirthday()->format('M')
                    ){
                        if($existe->getStatus() == TicketProspect::ST_REGISTERED){
                            $findOne = true;
                        }                        
                    }
                }
            }
        }

        if(!$findOne){
            foreach($existent as $existe){
                $existe->setIsDiff($diff);
                $em->persist($existe);
                array_push($doublons, $existe->getId());
            }
        }else{
            $prospect->setIsDiff($contraireDiff);
            $em->persist($prospect);
        }
        
        $em->flush();
        return $doublons;
    }

    /**
     * @Route("/selection/update/status", options={"expose"=true}, name="update_status_selection")
     */
    public function changeStatusSelection(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent());
        $selection = $data->selection;

        $prospects = $em->getRepository(TicketProspect::class)->findBy(array('id' => $selection));
        if(!$prospects){
            return new JsonResponse(['code' => 0]);
        }
        $arr = [];
        foreach($prospects as $prospect){
            $st = $prospect->getStatus() == TicketProspect::ST_CONFIRMED ? TicketProspect::ST_REGISTERED : TicketProspect::ST_CONFIRMED;
            $stString = $st == TicketProspect::ST_CONFIRMED ? "Attente" : "Inscrit";
            $prospect->setStatus($st);
            array_push($arr, [
                'id' => $prospect->getId(),
                'status' => $st,
                'statusString' => $stString
            ]);

            $em->persist($prospect);
        }
        $em->flush();

        return new JsonResponse(['code' => 1, 'prospects' => $arr]);
    }

    /**
     * @Route("/eleve/{id}/delete", options={"expose"=true}, name="delete")
     */
    public function deleteProspect(TicketProspect $id, Remaining $remaining, ResponsableService $responsableService)
    {
        $em = $this->getDoctrine()->getManager();

        $prospect = $id;
        if(!$prospect){
            return new JsonResponse(['code' => 0]);
        }
        $this->deleteP($prospect, $responsableService);

        $em->flush();
        return new JsonResponse(['code' => 1]);
    }

    /**
     * @Route("/selection/delete", options={"expose"=true}, name="delete_selection")
     */
    public function deleteSelection(Request $request, ResponsableService $responsableService)
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent());
        $selection = $data->selection;

        $prospects = $em->getRepository(TicketProspect::class)->findBy(array('id' => $selection));
        if(!$prospects){
            return new JsonResponse(['code' => 0]);
        }

        foreach($prospects as $prospect){
            $this->deleteP($prospect, $responsableService);
        }
        $em->flush();

        return new JsonResponse(['code' => 1]);
    }

    private function deleteP($prospect, ResponsableService $responsableService){
        $em = $this->getDoctrine()->getManager();
        $responsable = $prospect->getResponsable();
        $prospects = $responsable->getProspects();
        $nbProspects = count($prospects);

        if($nbProspects == 1){
            $responsableService->deleteResponsable($responsable);
        }else{
            $em->remove($prospect);
        }
    }

    /**
    * @Route("/eleve/{id}/get/infos", options={"expose"=true}, name="get_infos")
    */
    public function getProspect(TicketProspect $id, SerializerInterface $serializer)
    {
        $em = $this->getDoctrine()->getManager();

        $prospect = $serializer->serialize($id, 'json', ['attributes' => [
            'id', 'firstname', 'lastname', 'civility', 'email', 'birthday', 'birthdayString', 'birthdayJavascript', 'age', 'phoneDomicile', 'phoneMobile', 'adr', 'cp', 'city',
            'numAdh', 'status', 'statusString', 'adherent' => ['id'], 'isDiff',
            'responsable' => ['id', 'civility', 'firstname', 'lastname', 'createAtString', 'adresseString', 'email', 'phoneMobile', 'phoneDomicile', 'ticket'], 
            'creneau' => ['id', 'horaireString']
        ]]);

        return new JsonResponse(['code' => 1, 'prospect' => $prospect]);
    }

    /**
     * @Route("/eleve/{id}/set/infos", options={"expose"=true}, name="set_infos")
     */
    public function setProspect(Request $request, TicketProspect $id, SerializerInterface $serializer)
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent());
        $prospect = $data->prospect;

        $birthday = new DateTime(date("Y-m-d", strtotime(str_replace('/', '-', $prospect->birthday))));
        $numAdh = $prospect->numAdh == "" ? null : $prospect->numAdh;

        $existe = $em->getRepository(TicketProspect::class)->findOneBy(array(
            'civility' => $prospect->civility,
            'firstname' => $prospect->firstname,
            'lastname' => $prospect->lastname,
            'email' => $prospect->email,
            'birthday' => $birthday,
            'numAdh' => $numAdh
        ));

        if($existe){
            if($existe->getId() != $id->getId()){
                return new JsonResponse(['code' => 0, 'message' => 'Les informations entrées correspondent à un autre élève.']);
            }
    
            if($existe->getId() == $id->getId()){
                return new JsonResponse(['code' => 2, 'message' => ""]);
            }
        }

        $id->setCivility($prospect->civility);
        $id->setFirstname($prospect->firstname);
        $id->setLastname($prospect->lastname);
        $id->setEmail($prospect->email);
        $id->setBirthday($birthday);
        $id->setPhoneMobile($prospect->phoneMobile);
        $id->setNumAdh($numAdh);

        $em->persist($id);
        $em->flush();

        $prospectEdit = $serializer->serialize($id, 'json', ['attributes' => [
            'id', 'firstname', 'lastname', 'civility', 'email', 'birthday', 'birthdayString', 'birthdayJavascript', 'age', 'phoneDomicile', 'phoneMobile', 'adr', 'cp', 'city',
            'numAdh', 'status', 'statusString', 'adherent' => ['id'], 'isDiff',
            'responsable' => ['id', 'civility', 'firstname', 'lastname', 'createAtString', 'adresseString', 'email', 'phoneMobile', 'phoneDomicile', 'ticket'], 
            'creneau' => ['id', 'horaireString']
        ]]);

        $prospect = $serializer->serialize($id, 'json', ['attributes' => [
            'id', 'firstname', 'lastname', 'civility', 'email', 'birthday', 'age', 'phoneDomicile', 'phoneMobile', 'adr', 'cp', 'city',
            'numAdh', 'status', 'statusString', 'isDiff',
            'responsable' => ['id', 'civility', 'firstname', 'lastname', 'email'],
            'day' => ['id', 'type', 'typeString']
        ]]);

        return new JsonResponse(['code' => 1, 'prospectEdit' => $prospectEdit, 'prospect' => $prospect]);
    }
}
