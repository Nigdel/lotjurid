<?php

namespace App\Repository;

use App\Entity\Extension;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Extension|null find($id, $lockMode = null, $lockVersion = null)
 * @method Extension|null findOneBy(array $criteria, array $orderBy = null)
 * @method Extension[]    findAll()
 * @method Extension[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExtensionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Extension::class);
    }

     /**
      * @return Extension[] Returns an array of Extension objects
      */
    public function findTerrestre()
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.id >= 1 and e.id <=4 and e.id <>3')
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?Extension
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
