<?php

namespace App\Repository;

use App\Entity\FolioLot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FolioLot|null find($id, $lockMode = null, $lockVersion = null)
 * @method FolioLot|null findOneBy(array $criteria, array $orderBy = null)
 * @method FolioLot[]    findAll()
 * @method FolioLot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FolioLotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FolioLot::class);
    }

    // /**
    //  * @return FolioLot[] Returns an array of FolioLot objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FolioLot
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
