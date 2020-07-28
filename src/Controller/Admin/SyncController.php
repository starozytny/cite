<?php

namespace App\Controller\Admin;

use App\Entity\Cite\CiPersonne;
use App\Entity\TicketResponsable;
use App\Service\Export;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/sync", name="admin_sync_")
 */
class SyncController extends AbstractController
{
    /**
     * @Route("/data", name="data")
     */
    public function index(Export $export)
    {
        $em = $this->getDoctrine()->getManager();

        return new JsonResponse(['code' => 1]);
    }
}
