<?php

namespace App\Repository;

use App\Entity\TipoCombustible;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TipoCombustible|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipoCombustible|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipoCombustible[]    findAll()
 * @method TipoCombustible[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipoCombustibleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipoCombustible::class);
    }

    // /**
    //  * @return TipoCombustible[] Returns an array of TipoCombustible objects
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
    public function findOneBySomeField($value): ?TipoCombustible
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
