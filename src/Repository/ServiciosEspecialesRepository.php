<?php

namespace App\Repository;

use App\Entity\ServiciosEspeciales;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ServiciosEspeciales|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServiciosEspeciales|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServiciosEspeciales[]    findAll()
 * @method ServiciosEspeciales[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiciosEspecialesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiciosEspeciales::class);
    }

    // /**
    //  * @return ServiciosEspeciales[] Returns an array of ServiciosEspeciales objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ServiciosEspeciales
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
