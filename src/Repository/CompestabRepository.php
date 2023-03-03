<?php

namespace App\Repository;

use App\Entity\Compestab;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Compestab|null find($id, $lockMode = null, $lockVersion = null)
 * @method Compestab|null findOneBy(array $criteria, array $orderBy = null)
 * @method Compestab[]    findAll()
 * @method Compestab[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompestabRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Compestab::class);
    }
    public function getWithSearchQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $term)
//            ->orderBy('c.id', 'ASC')
            ;
    }
    public function searchInsta(string $nombre){
        return $this->createQueryBuilder('c')
            ->innerJoin('c.instalacion','i')
            ->andWhere('i.nombre  like :val')
            ->setParameter('val','%'.$nombre.'%');
    }

    public function searchFolio(string $folio){
        return $this->createQueryBuilder('c')
            ->andWhere('c.folio  like :val')
            ->setParameter('val','%'.$folio.'%');
    }

    public function searchLot(string $lot){
        return $this->createQueryBuilder('c')
            ->innerJoin('c.instalacion','i')
            ->innerJoin('i.lot','l')
            ->andWhere('l.id  like :val')
            ->setParameter('val','%'.$lot.'%');
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
}
