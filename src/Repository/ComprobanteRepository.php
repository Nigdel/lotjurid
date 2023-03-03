<?php

namespace App\Repository;

use App\Entity\Comprobante;
use App\Entity\MediosTrans;
use App\Entity\Municipios;
use App\Entity\Provincias;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comprobante|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comprobante|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comprobante[]    findAll()
 * @method Comprobante[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComprobanteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comprobante::class);
    }
    public function getWithSearchQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $term)
            ->orderBy('c.id', 'ASC')
            ;
    }
    public function searchMedio(string $nombre){
        return $this->createQueryBuilder('c')
            ->innerJoin('c.medio','m')
            ->andWhere('m.nombre  like :val')
            ->setParameter('val','%'.$nombre.'%');
    }

    public function searchFolio(string $folio){
        return $this->createQueryBuilder('c')
            ->andWhere('c.folio  like :val')
            ->setParameter('val','%'.$folio.'%');
    }

    // /**
    //  * @return Comprobante[] Returns an array of Comprobante objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Comprobante
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function search($term)
    {
        return $this->createQueryBuilder('comp')
            ->andWhere('comp.folio LIKE :searchTerm')
            ->setParameter('searchTerm', '%'.$term.'%')
            ->getQuery()
            ->execute();
    }
    /**
     * @return MediosTrans[] Returns an array of MediosTrans objects
     */
    public function findMediosConCompVigbyMun(Municipios $mun)
    {
        return $this->createQueryBuilder('c')
            ->join('c.medio','m')
            ->join('m.basificacionObj','b')
            ->andWhere('b.idmun = :val')
            ->andWhere('c.estadoComp = 4')
            ->setParameter('val', $mun->getId())
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return MediosTrans[] Returns an array of MediosTrans objects
     */
    public function findMediosConCompSuspbyMun(Municipios $mun)
    {
        return $this->createQueryBuilder('c')
            ->join('c.medio','m')
            ->join('m.basificacionObj','b')
            ->andWhere('b.idmun = :val')
            ->andWhere('c.estadoComp = 6')
            ->setParameter('val', $mun->getId())
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Comprobante[] Returns an array of Comprobante ptes de impresion
     */
    public function findConCompPtesImpres(User $user=null)
    {
        return $this->createQueryBuilder('c')
            ->join('c.medio','m')
            ->addSelect('m')
            ->join('m.basificacionObj','b')
            ->addSelect('b')
            ->join('b.idmun','idmun')
            ->addSelect('idmun')
            ->join('idmun.provinciaid','p')
            ->addSelect('p')
            ->andWhere('p.id = :u')
            ->andWhere('c.estadoComp = 2')
            ->setParameter('u',$user->getMunicipio()->getProvinciaid()->getId())
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Comprobante[] Returns an array of Comprobante ptes de impresion
     */
    public function findConCompPtesEntrega(User $user=null)
    {
        return $this->createQueryBuilder('c')
            ->join('c.medio','m')
            ->addSelect('m')
            ->join('m.basificacionObj','b')
            ->addSelect('b')
            ->join('b.idmun','idmun')
            ->addSelect('idmun')
            ->join('idmun.provinciaid','p')
            ->addSelect('p')
            ->andWhere('p.id = :u')
            ->andWhere('c.estadoComp = 3')
            ->setParameter('u',$user->getMunicipio()->getProvinciaid()->getId())
            ->getQuery()
            ->getResult()
            ;
    }
}
