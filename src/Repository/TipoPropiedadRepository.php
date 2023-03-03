<?php

namespace App\Repository;

use App\Entity\TipoPropiedad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TipoPropiedad|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipoPropiedad|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipoPropiedad[]    findAll()
 * @method TipoPropiedad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipoPropiedadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipoPropiedad::class);
    }

    // /**
    //  * @return TipoPropiedad[] Returns an array of TipoPropiedad objects
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
    public function findOneBySomeField($value): ?TipoPropiedad
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
