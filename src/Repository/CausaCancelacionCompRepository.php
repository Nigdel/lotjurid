<?php

namespace App\Repository;

use App\Entity\CausaCancelacionComp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CausaCancelacionComp|null find($id, $lockMode = null, $lockVersion = null)
 * @method CausaCancelacionComp|null findOneBy(array $criteria, array $orderBy = null)
 * @method CausaCancelacionComp[]    findAll()
 * @method CausaCancelacionComp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CausaCancelacionCompRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CausaCancelacionComp::class);
    }

    // /**
    //  * @return CausaCancelacionComp[] Returns an array of CausaCancelacionComp objects
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
    public function findOneBySomeField($value): ?CausaCancelacionComp
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
