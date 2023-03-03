<?php

namespace App\Repository;

use App\Entity\FolioCompMT;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FolioCompMT|null find($id, $lockMode = null, $lockVersion = null)
 * @method FolioCompMT|null findOneBy(array $criteria, array $orderBy = null)
 * @method FolioCompMT[]    findAll()
 * @method FolioCompMT[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FolioCompMTRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FolioCompMT::class);
    }

    // /**
    //  * @return FolioCompMT[] Returns an array of FolioCompMT objects
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
    public function findOneBySomeField($value): ?FolioCompMT
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
