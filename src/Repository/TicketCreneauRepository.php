<?php

namespace App\Repository;

use App\Entity\TicketCreneau;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TicketCreneau|null find($id, $lockMode = null, $lockVersion = null)
 * @method TicketCreneau|null findOneBy(array $criteria, array $orderBy = null)
 * @method TicketCreneau[]    findAll()
 * @method TicketCreneau[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketCreneauRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TicketCreneau::class);
    }

    // /**
    //  * @return TicketCreneau[] Returns an array of TicketCreneau objects
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
    public function findOneBySomeField($value): ?TicketCreneau
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
