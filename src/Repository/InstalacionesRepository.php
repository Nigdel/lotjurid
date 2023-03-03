<?php

namespace App\Repository;

use App\Entity\Instalaciones;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
// * @method Instalaciones|null find($id, $lockMode = null, $lockVersion = null)
 * @method Instalaciones|null findOneBy(array $criteria, array $orderBy = null)
 * @method Instalaciones[]    findAll()
 * @method Instalaciones[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InstalacionesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Instalaciones::class);
    }
    public function getWithSearchQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.lot','l')
            ->addSelect('l')
            ->leftJoin('l.identidad','pj')
            ->addSelect('pj')
            ->leftJoin('pj.idmunicipio','m')
            ->addSelect('m')
            ->leftJoin('m.provinciaid','p')
            ->addSelect('p')
            ->leftJoin('l.idestado','e')
            ->addSelect('e')
            ->leftJoin('l.idrama','r')
            ->addSelect('r')
            ->leftJoin('l.idextension','ex')
            ->addSelect('ex')
            ->leftJoin('l.basificaciones','b')
            ->addSelect('b')
            ;
    }

    public function searchNombre($search){
        return $this->createQueryBuilder('i')
            ->andWhere('i.nombre  like :val')
            ->setParameter('val','%'.$search.'%');
    }

    public function searchLot($search){
        return $this->createQueryBuilder('i')
            ->innerJoin('i.lot','l')
            ->addSelect('l')
            ->andWhere('l.id  like :val')
            ->setParameter('val','%'.$search.'%');
    }
    // /**
    //  * @return Instalaciones[] Returns an array of Instalaciones objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Instalaciones
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    /**
     * @param $id
     * @param null $lockMode
     * @param null $lockVersion
     * @return mixed|null|Instalaciones
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function find($id, $lockMode = null, $lockVersion = null){
        return $this->createQueryBuilder('i')
            ->leftJoin('i.lot','l')
            ->addSelect('l')
            ->leftJoin('l.identidad','pj')
            ->addSelect('pj')
            ->leftJoin('pj.idmunicipio','m')
            ->addSelect('m')
            ->leftJoin('m.provinciaid','p')
            ->addSelect('p')
            ->leftJoin('l.idestado','e')
            ->addSelect('e')
            ->leftJoin('l.idrama','r')
            ->addSelect('r')
            ->leftJoin('l.idextension','ex')
            ->addSelect('ex')
            ->leftJoin('l.basificaciones','b')
            ->addSelect('b')
            ->andWhere('i = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getSingleResult()
            ;
    }
}
