<?php

namespace App\Repository;

use App\Entity\MediosTrans;
use App\Entity\Municipios;
use App\Entity\Provincias;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MediosTrans|null find($id, $lockMode = null, $lockVersion = null)
 * @method MediosTrans|null findOneBy(array $criteria, array $orderBy = null)
 * @method MediosTrans[]    findAll()
 * @method MediosTrans[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediosTransRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MediosTrans::class);
    }

    public function getWithSearchQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('m');
    }

    public function searchNombre($search){
        return $this->createQueryBuilder('m')
            ->andWhere('m.nombre  like :val')
            ->setParameter('val','%'.$search.'%');
    }

    public function searchLot($search){
        return $this->createQueryBuilder('m')
            ->innerJoin('m.basificacionObj','b')
            ->addSelect('b')
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

    /**
    * @return MediosTrans[] Returns an array of MediosTrans objects
    */
    public function findMediosbyMun(Municipios $mun)
    {
        return $this->createQueryBuilder('m')
            ->join('m.basificacionObj','b')
            ->andWhere('b.idmun = :val')
            ->setParameter('val', $mun->getId())
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return MediosTrans[] Returns an array of MediosTrans objects
     */
    public function findMediosAptosbyMun(Municipios $mun)
    {
        return $this->createQueryBuilder('m')
            ->join('m.basificacionObj','b')
            ->andWhere('b.idmun = :val')
            ->andWhere('m.estadoMedio = 1')
            ->setParameter('val', $mun->getId())
            ->getQuery()
            ->getResult()
            ;
    }
    /**
     * @return MediosTrans[] Returns an array of MediosTrans objects
     */
    public function findMediosbyProv(Provincias $prov)
    {
        return $this->createQueryBuilder('m')
            ->join('m.basificacionObj','b')
            ->join('b.idmun','mun')
            ->andWhere('mun.provinciaid = :val')
            ->setParameter('val', $prov->getId())
            ->getQuery()
            ->getResult()
            ;
    }
    /**
     * @return MediosTrans[] Returns an array of MediosTrans objects
     */
    public function findMediosAptosbyProv(Provincias $prov)
    {
        return $this->createQueryBuilder('m')
            ->join('m.basificacionObj','b')
            ->join('b.idmun','mun')
            ->andWhere('mun.provinciaid = :val')
            ->andWhere('m.estadoMedio = 1')
            ->setParameter('val', $prov->getId())
            ->getQuery()
            ->getResult()
            ;
    }

}
