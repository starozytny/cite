<?php

namespace App\Repository;

use App\Entity\TicketOuverture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TicketOuverture|null find($id, $lockMode = null, $lockVersion = null)
 * @method TicketOuverture|null findOneBy(array $criteria, array $orderBy = null)
 * @method TicketOuverture[]    findAll()
 * @method TicketOuverture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketOuvertureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TicketOuverture::class);
    }

    // /**
    //  * @return TicketOuverture[] Returns an array of TicketOuverture objects
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
    public function findOneBySomeField($value): ?TicketOuverture
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
