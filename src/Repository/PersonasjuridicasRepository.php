<?php

namespace App\Repository;

use App\Entity\Personasjuridicas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Personasjuridicas|null find($id, $lockMode = null, $lockVersion = null)
 * @method Personasjuridicas|null findOneBy(array $criteria, array $orderBy = null)
 * @method Personasjuridicas[]    findAll()
 * @method Personasjuridicas[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonasjuridicasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Personasjuridicas::class);
    }
    public function getWithSearchQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('p');
    }
    public function searchExpediente(string $expediente){
        return $this->createQueryBuilder('p')
            ->leftJoin('p.lot','l')
            ->addSelect('l')
            ->andWhere('l.id  like :val')
            ->setParameter('val','%'.$expediente.'%');
    }

    public function searchNombre(string $nombre){
        return $this->createQueryBuilder('p')
            ->andWhere('p.nomentidad  like :val')
            ->setParameter('val','%'.$nombre.'%');
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

    public function search($term)
    {
        return $this->createQueryBuilder('comp')
            ->andWhere('comp.folio LIKE :searchTerm')
            ->setParameter('searchTerm', '%'.$term.'%')
            ->getQuery()
            ->execute();
    }*/
}
