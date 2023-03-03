<?php

namespace App\Repository;

use App\Entity\OficinaMcpal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OficinaMcpal|null findOneBy(array $criteria, array $orderBy = null)
 * @method OficinaMcpal[]    findAll()
 * @method OficinaMcpal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OficinaMcpalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OficinaMcpal::class);
    }

    // /**
    //  * @return OficinaMcpal[] Returns an array of OficinaMcpal objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OficinaMcpal
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function find($id, $lockMode = null, $lockVersion = null){
        return $this->createQueryBuilder('om')
            ->join('om.direccionProvincial','dp')
            ->addSelect('dp')
            ->andWhere('om.id = :var')
            ->setParameter('var',$id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
