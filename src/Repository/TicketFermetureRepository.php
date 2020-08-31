<?php

namespace App\Repository;

use App\Entity\TicketFermeture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TicketFermeture|null find($id, $lockMode = null, $lockVersion = null)
 * @method TicketFermeture|null findOneBy(array $criteria, array $orderBy = null)
 * @method TicketFermeture[]    findAll()
 * @method TicketFermeture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketFermetureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TicketFermeture::class);
    }

    // /**
    //  * @return TicketFermeture[] Returns an array of TicketFermeture objects
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
    public function findOneBySomeField($value): ?TicketFermeture
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
