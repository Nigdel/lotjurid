<?php

namespace App\Repository;

use App\Entity\CausaCancelacionLot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CausaCancelacionLot|null find($id, $lockMode = null, $lockVersion = null)
 * @method CausaCancelacionLot|null findOneBy(array $criteria, array $orderBy = null)
 * @method CausaCancelacionLot[]    findAll()
 * @method CausaCancelacionLot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CausaCancelacionLotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CausaCancelacionLot::class);
    }

    // /**
    //  * @return CausaCancelacionLot[] Returns an array of CausaCancelacionLot objects
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
    public function findOneBySomeField($value): ?CausaCancelacionLot
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
