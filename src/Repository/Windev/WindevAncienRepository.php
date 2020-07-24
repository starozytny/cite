<?php

namespace App\Repository\Windev;

use App\Entity\Windev\WindevAncien;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WindevAncien|null find($id, $lockMode = null, $lockVersion = null)
 * @method WindevAncien|null findOneBy(array $criteria, array $orderBy = null)
 * @method WindevAncien[]    findAll()
 * @method WindevAncien[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WindevAncienRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WindevAncien::class);
    }

    // /**
    //  * @return WindevAncien[] Returns an array of WindevAncien objects
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
    public function findOneBySomeField($value): ?WindevAncien
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
