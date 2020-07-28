<?php

namespace App\Controller\Admin;

use App\Entity\TicketResponsable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/admin/responsable", name="admin_responsable_")
 */
class ResponsableController extends AbstractController
{
    /**
     * @Route("/edit/{responsable}", options={"expose"=true}, name="edit")
     */
    public function edit(TicketResponsable $responsable, SerializerInterface $serializer)
    {
        $resp = $serializer->serialize($responsable, 'json', ['attributes' => [
            'id', 'firstname', 'lastname', 'civility', 'email', 'phoneDomicile', 'phoneMobile', 'adr', 'complement', 'cp', 'city'
        ]]);

        return $this->render('root/admin/pages/ticket/responsable.html.twig', [
            'responsable' => $responsable,
            'resp' => $resp
        ]);
    }
/**
     * @Route("/edit/{responsable}/set/infos", options={"expose"=true}, name="update")
     */
    public function update(TicketResponsable $responsable, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent());
        $resp = $data->responsable;

        $existe = $em->getRepository(TicketResponsable::class)->findOneBy(array(
            'civility' => $resp->civility,
            'firstname' => $resp->firstname,
            'lastname' => $resp->lastname,
            'email' => $resp->email
        ));

        if($existe){
            if($existe->getId() != $responsable->getId()){
                return new JsonResponse(['code' => 0, 'message' => 'Les informations entrées correspondent à un autre responsable.']);
            }
    
            if($existe->getId() == $responsable->getId()){
                return new JsonResponse(['code' => 2, 'message' => ""]);
            }
        }

        $responsable->setCivility($resp->civility);
        $responsable->setFirstname($resp->firstname);
        $responsable->setLastname($resp->lastname);
        $responsable->setEmail($resp->email);
        $responsable->setPhoneMobile($resp->phoneMobile);
        $responsable->setPhoneDomicile($resp->phoneDomicile);
        $responsable->setAdr($resp->adr);
        $responsable->setCp($resp->cp);
        $responsable->setCity($resp->city);


        $em->persist($responsable);
        $em->flush();

        return new JsonResponse(['code' => 1]);
    }
}
