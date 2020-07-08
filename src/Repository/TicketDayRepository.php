<?php

namespace App\Repository;

use App\Entity\TicketDay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TicketDay|null find($id, $lockMode = null, $lockVersion = null)
 * @method TicketDay|null findOneBy(array $criteria, array $orderBy = null)
 * @method TicketDay[]    findAll()
 * @method TicketDay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketDayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TicketDay::class);
    }

    // /**
    //  * @return TicketDay[] Returns an array of TicketDay objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TicketDay
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
