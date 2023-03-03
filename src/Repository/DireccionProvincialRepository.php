<?php

namespace App\Repository;

use App\Entity\DireccionProvincial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DireccionProvincial|null find($id, $lockMode = null, $lockVersion = null)
 * @method DireccionProvincial|null findOneBy(array $criteria, array $orderBy = null)
 * @method DireccionProvincial[]    findAll()
 * @method DireccionProvincial[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DireccionProvincialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DireccionProvincial::class);
    }

    // /**
    //  * @return DireccionProvincial[] Returns an array of DireccionProvincial objects
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
    public function findOneBySomeField($value): ?DireccionProvincial
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
