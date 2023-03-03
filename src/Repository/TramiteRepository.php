<?php

namespace App\Repository;

use App\Entity\Tramite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tramite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tramite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tramite[]    findAll()
 * @method Tramite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TramiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tramite::class);
    }

    // /**
    //  * @return Tramite[] Returns an array of Tramite objects
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
    public function findOneBySomeField($value): ?Tramite
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
