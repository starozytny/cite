<?php


namespace App\Service;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;

class CheckTime
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function deleteMoreFiveYear($date_check, $entity){
        $result = $this->moreThanFiveYear($date_check);

        if($result){
            $this->em->remove($entity);
            $this->em->flush();

            return 1;
        }
        return 0;
    }

    public function moreTenMinutes(?DateTimeInterface $datetime){
        if($datetime == null){
            return true;
        }
        return (time() > (date_timestamp_get($datetime) + (60*10))) ? true : false;
    }

    public function moreThirtyMinutes(?DateTimeInterface $datetime){
        if($datetime == null){
            return true;
        }
        return (time() > (date_timestamp_get($datetime) + (60*30))) ? true : false;
    }

    public function moreThanTwoMonth(DateTimeInterface $datetime)
    {
        $n_year = intval(date_format(new DateTime(), 'Y'));
        $n_month = intval(date_format(new DateTime(), 'm'));
        $year = intval(date_format($datetime,'Y'));
        $month = intval(date_format($datetime,'m'));

        if($year == $n_year){

            $checkMonth = $n_month - $month;

            if($checkMonth == 0){
                return 0;
            }else if($checkMonth > 2){
                return 1;
            }else{
                return 0;
            }
        }
        if($year < $n_year){
            if($month != 11 || $month != 12){
                return 1;
            }

            if($month == 11){
                if($n_month != 11 || $n_month != 12){
                    return 1;
                }else{
                    return 0;
                }
            }

            if($month == 12){
                if($n_month != 12 || $n_month != 1){
                    return 1;
                }else{
                    return 0;
                }
            }
        }

        return 0;
    }

    public function moreThanOneMonth(DateTimeInterface $datetime)
    {
        $n_year = intval(date_format(new DateTime(), 'Y'));
        $n_month = intval(date_format(new DateTime(), 'm'));
        $year = intval(date_format($datetime,'Y'));
        $month = intval(date_format($datetime,'m'));

        if($year == $n_year){

            $checkMonth = $n_month - $month;

            if($checkMonth == 0){
                return 0;
            }else if($checkMonth > 1){
                return 1;
            }else{
                return 0;
            }
        }
        if($year < $n_year){
            if($month != 12){
                return 1;
            }
        }

        return 0;
    }

    public function moreThanOneYear(DateTimeInterface $dateTime)
    {
        $n_year = date_format(new DateTime(), 'Y');
        $year = date_format($dateTime,'Y');

        $diff = $n_year - $year;

        if($diff == 0){
            return 0;
        }elseif ($diff > 1){
            return 1;
        }
    }

    public function moreThanFiveYear(DateTimeInterface $dateTime)
    {
        $n_year = date_format(new DateTime(), 'Y');
        $year = date_format($dateTime,'Y');

        $diff = $n_year - $year;

        if($diff == 0){
            return 0;
        }elseif ($diff > 5){
            return 1;
        }
    }
}
