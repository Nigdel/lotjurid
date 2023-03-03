<?php

namespace App\Repository;

use App\Entity\CausaSuspensionLot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CausaSuspensionLot|null find($id, $lockMode = null, $lockVersion = null)
 * @method CausaSuspensionLot|null findOneBy(array $criteria, array $orderBy = null)
 * @method CausaSuspensionLot[]    findAll()
 * @method CausaSuspensionLot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CausaSuspencionLotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CausaSuspensionLot::class);
    }

    // /**
    //  * @return CausaSuspensionLot[] Returns an array of CausaSuspensionLot objects
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
    public function findOneBySomeField($value): ?CausaSuspensionLot
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
