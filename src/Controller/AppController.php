<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Rgpd;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function index()
    {
        return $this->render('root/app/index.html.twig');
    }
    /**
     * @Route("/legales/mentions-legales", name="app_mentions")
     */
    public function mentions()
    {
        return $this->render('root/app/pages/legales/mentions.html.twig');
    }
    /**
     * @Route("/legales/politique-confidentialite", options={"expose"=true}, name="app_politique")
     */
    public function politique()
    {
        return $this->render('root/app/pages/legales/politique.html.twig');
    }
    /**
     * @Route("/legales/gestion-cookies", options={"expose"=true}, name="app_cookies")
     */
    public function cookies()
    {
        return $this->render('root/app/pages/legales/cookies.html.twig');
    }
    /**
     * @Route("/legales/demande-rgpd", options={"expose"=true}, name="app_rgpd")
     */
    public function rgpd(Request $request, Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();

        if($request->isMethod("POST")){
            $data = json_decode($request->getContent());
            $firstname = $data->firstname->value;
            $email = $data->email->value;
            $subject = $data->subject->value;
            $message = $data->message->value;

            $demande = (new Rgpd())
                ->setFirstname($firstname)
                ->setEmail($email)
                ->setSubject($subject)
                ->setMessage($message)
            ;

            // Send mail       
            if($mailer->sendMail(
                'Demande RGPD',
                'Deamnde RGPD via le site Logilink',
                'root/app/email/legales/rgpd.html.twig',
                ['rgpd' => $demande],
                'chanbora@logilink.fr'
            ) != true){
                return new JsonResponse([
                    'code' => 2,
                    'errors' => [ 'error' => 'Le service est indisponible', 'success' => '' ]
                ]);
            }

            $em->persist($demande); $em->flush();

            return new JsonResponse(['code' => 1, 'message' => 'La demande a été envoyé.']);
        }

        return $this->render('root/app/pages/legales/rgpd.html.twig');
    }
    /**
     * @Route("/nous-contacter", options={"expose"=true}, name="app_contact")
     */
    public function contact(Request $request, Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();

        if($request->isMethod("POST")){
            $data = json_decode($request->getContent());
            $firstname = $data->firstname->value;
            $email = $data->email->value;
            $message = $data->message->value;

            $demande = (new Contact())
                ->setFirstname($firstname)
                ->setEmail($email)
                ->setMessage($message)
            ;

            // Send mail       
            if($mailer->sendMail(
                'Demande de contact',
                'Deamnde contact via le site Logilink',
                'root/app/email/contact/index.html.twig',
                ['contact' => $demande],
                'chanbora@logilink.fr'
            ) != true){
                return new JsonResponse([
                    'code' => 2,
                    'errors' => [ 'error' => 'Le service est indisponible', 'success' => '' ]
                ]);
            }

            $em->persist($demande); $em->flush();

            return new JsonResponse(['code' => 1, 'message' => 'La demande a été envoyé.']);
        }

        return $this->render('root/app/pages/contact/index.html.twig');
    }
}
