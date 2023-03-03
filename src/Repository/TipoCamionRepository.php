<?php

namespace App\Repository;

use App\Entity\TipoCamion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TipoCamion|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipoCamion|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipoCamion[]    findAll()
 * @method TipoCamion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipoCamionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipoCamion::class);
    }

    // /**
    //  * @return TipoCamion[] Returns an array of TipoCamion objects
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
    public function findOneBySomeField($value): ?TipoCamion
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
