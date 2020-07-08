<?php

namespace App\Controller\App;

use App\Service\OpenDay;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/reservation", name="app_booking_")
 */
class BookingController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(OpenDay $openDay)
    {
        $openDay->open();

        return $this->render('root/app/pageS/booking/index.html.twig');
    }
}
