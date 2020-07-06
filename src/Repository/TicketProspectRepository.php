<?php

namespace App\Repository;

use App\Entity\TicketProspect;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TicketProspect|null find($id, $lockMode = null, $lockVersion = null)
 * @method TicketProspect|null findOneBy(array $criteria, array $orderBy = null)
 * @method TicketProspect[]    findAll()
 * @method TicketProspect[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketProspectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TicketProspect::class);
    }

    // /**
    //  * @return TicketProspect[] Returns an array of TicketProspect objects
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
    public function findOneBySomeField($value): ?TicketProspect
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
