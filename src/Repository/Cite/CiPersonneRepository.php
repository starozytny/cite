<?php

namespace App\Repository\Cite;

use App\Entity\Cite\CiPersonne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CiPersonne|null find($id, $lockMode = null, $lockVersion = null)
 * @method CiPersonne|null findOneBy(array $criteria, array $orderBy = null)
 * @method CiPersonne[]    findAll()
 * @method CiPersonne[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CiPersonneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CiPersonne::class);
    }

    // /**
    //  * @return CiPersonne[] Returns an array of CiPersonne objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CiPersonne
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
