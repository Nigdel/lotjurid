<?php

namespace App\Repository;

use App\Entity\EstadoMensaje;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EstadoMensaje|null find($id, $lockMode = null, $lockVersion = null)
 * @method EstadoMensaje|null findOneBy(array $criteria, array $orderBy = null)
 * @method EstadoMensaje[]    findAll()
 * @method EstadoMensaje[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EstadoMensajeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EstadoMensaje::class);
    }

    // /**
    //  * @return EstadoMensaje[] Returns an array of EstadoMensaje objects
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
    public function findOneBySomeField($value): ?EstadoMensaje
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
