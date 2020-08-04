<?php

namespace App\Repository\Windev;

use App\Entity\Windev\WindevPersonne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WindevPersonne|null find($id, $lockMode = null, $lockVersion = null)
 * @method WindevPersonne|null findOneBy(array $criteria, array $orderBy = null)
 * @method WindevPersonne[]    findAll()
 * @method WindevPersonne[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WindevPersonneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WindevPersonne::class);
    }

    // /**
    //  * @return WindevPersonne[] Returns an array of WindevPersonne objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WindevPersonne
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
