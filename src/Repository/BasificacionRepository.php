<?php

namespace App\Repository;

use App\Entity\Basificacion;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Basificacion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Basificacion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Basificacion[]    findAll()
 * @method Basificacion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BasificacionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Basificacion::class);
    }

    public function getWithSearchQueryBuilder(User $user=null): QueryBuilder
    {
        return $this->createQueryBuilder('b')
                ->leftJoin('b.idlicencia','l')
                ->addSelect('l')
                ->leftJoin('b.idmun','m')
                ->addSelect('m')
                ->leftJoin('m.provinciaid','p')
                ->addSelect('p')
                ->andWhere('p.id = :u')
                ->setParameter('u',$user->getMunicipio()->getProvinciaid()->getId())
                ;
    }

    public function searchNombre($search){
        return $this->createQueryBuilder('b')
            ->andWhere('b.nombrelb  like :val')
            ->setParameter('val','%'.$search.'%');
    }

    public function searchLot($search){
        return $this->createQueryBuilder('b')
            ->innerJoin('b.idlicencia','l')
            ->addSelect('l')
            ->andWhere('l.id  like :val')
            ->setParameter('val','%'.$search.'%');
    }
    // /**
    //  * @return MediosTrans[] Returns an array of MediosTrans objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MediosTrans
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
