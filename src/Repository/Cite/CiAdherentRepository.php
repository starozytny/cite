<?php

namespace App\Repository\Cite;

use App\Entity\Cite\CiAdherent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CiAdherent|null find($id, $lockMode = null, $lockVersion = null)
 * @method CiAdherent|null findOneBy(array $criteria, array $orderBy = null)
 * @method CiAdherent[]    findAll()
 * @method CiAdherent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CiAdherentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CiAdherent::class);
    }

    // /**
    //  * @return Adherent[] Returns an array of Adherent objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Adherent
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
