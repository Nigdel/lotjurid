<?php

namespace App\Repository;

use App\Entity\DireccionNacional;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DireccionNacional|null find($id, $lockMode = null, $lockVersion = null)
 * @method DireccionNacional|null findOneBy(array $criteria, array $orderBy = null)
 * @method DireccionNacional[]    findAll()
 * @method DireccionNacional[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DireccionNacionalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DireccionNacional::class);
    }


    // /**
    //  * @return DireccionNacional[] Returns an array of DireccionNacional objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DireccionNacional
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
