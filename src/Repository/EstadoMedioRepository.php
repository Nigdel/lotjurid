<?php

namespace App\Repository;

use App\Entity\EstadoMedio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EstadoMedio|null find($id, $lockMode = null, $lockVersion = null)
 * @method EstadoMedio|null findOneBy(array $criteria, array $orderBy = null)
 * @method EstadoMedio[]    findAll()
 * @method EstadoMedio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EstadoMedioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EstadoMedio::class);
    }

    // /**
    //  * @return EstadoMedio[] Returns an array of EstadoMedio objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EstadoMedio
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
