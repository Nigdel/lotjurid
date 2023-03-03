<?php

namespace App\Repository;

use App\Entity\Tipotramite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tipotramite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tipotramite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tipotramite[]    findAll()
 * @method Tipotramite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipotramiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tipotramite::class);
    }

    // /**
    //  * @return Tipotramite[] Returns an array of Tipotramite objects
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
    public function findOneBySomeField($value): ?Tipotramite
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
