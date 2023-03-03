<?php

namespace App\Repository;

use App\Entity\Umcap;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Umcap|null find($id, $lockMode = null, $lockVersion = null)
 * @method Umcap|null findOneBy(array $criteria, array $orderBy = null)
 * @method Umcap[]    findAll()
 * @method Umcap[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UmcapRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Umcap::class);
    }

    // /**
    //  * @return Umcap[] Returns an array of Umcap objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Umcap
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
