<?php

namespace App\Controller\Admin;

use App\Entity\TicketProspect;
use App\Service\Remaining;
use App\Service\ResponsableService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/prospect", name="admin_prospect_")
 */
class ProspectController extends AbstractController
{
    /**
     * @Route("/eleve/{id}/update/status", options={"expose"=true}, name="update_status")
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
            $stString = $st == TicketProspect::ST_CONFIRMED ? "Confirmé" : "Inscrit";
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
}
