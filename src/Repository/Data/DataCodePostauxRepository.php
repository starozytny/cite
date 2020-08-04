<?php

namespace App\Repository\Data;

use App\Entity\Data\DataCodePostaux;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DataCodePostaux|null find($id, $lockMode = null, $lockVersion = null)
 * @method DataCodePostaux|null findOneBy(array $criteria, array $orderBy = null)
 * @method DataCodePostaux[]    findAll()
 * @method DataCodePostaux[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DataCodePostauxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DataCodePostaux::class);
    }

    // /**
    //  * @return DataCodePostaux[] Returns an array of DataCodePostaux objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DataCodePostaux
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
