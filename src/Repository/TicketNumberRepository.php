<?php

namespace App\Repository;

use App\Entity\TicketNumber;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TicketNumber|null find($id, $lockMode = null, $lockVersion = null)
 * @method TicketNumber|null findOneBy(array $criteria, array $orderBy = null)
 * @method TicketNumber[]    findAll()
 * @method TicketNumber[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketNumberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TicketNumber::class);
    }

    // /**
    //  * @return TicketNumber[] Returns an array of TicketNumber objects
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
    public function findOneBySomeField($value): ?TicketNumber
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
