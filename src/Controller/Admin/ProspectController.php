<?php

namespace App\Controller\Admin;

use App\Entity\TicketProspect;
use App\Service\Remaining;
use App\Service\ResponsableService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/prospect", name="admin_prospect_")
 */
class ProspectController extends AbstractController
{
    /**
     * @Route("/{id}/update/status", options={"expose"=true}, name="update_status")
     */
    public function changeStatus(TicketProspect $id)
    {
        $em = $this->getDoctrine()->getManager();

        $prospect = $id;
        if(!$prospect){
            return new JsonResponse(['code' => 0]);
        }

        if($prospect->getStatus() == TicketProspect::ST_CONFIRMED){
            $prospect->setStatus(TicketProspect::ST_REGISTERED);
            $status = TicketProspect::ST_REGISTERED;
            $statusString = "Inscrit";
        }else{
            $prospect->setStatus(TicketProspect::ST_CONFIRMED);
            $status = TicketProspect::ST_CONFIRMED;
            $statusString = "Confirmé";
        }

        $em->persist($prospect);
        $em->flush();

        return new JsonResponse(['code' => 1, 'status' => $status, 'statusString' => $statusString]);
    }

    /**
     * @Route("/{id}/delete", options={"expose"=true}, name="delete")
     */
    public function deleteProspect(TicketProspect $id, Remaining $remaining, ResponsableService $responsableService)
    {
        $em = $this->getDoctrine()->getManager();

        $prospect = $id;
        if(!$prospect){
            return new JsonResponse(['code' => 0]);
        }
        $responsable = $prospect->getResponsable();
        $prospects = $responsable->getProspects();
        $nbProspects = count($prospects);

        if($nbProspects == 1){
            $responsableService->deleteResponsable($responsable);
        }else{
            $em->remove($prospect);
        }

        $em->flush();
        return new JsonResponse(['code' => 1]);
    }
}