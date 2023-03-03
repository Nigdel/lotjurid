<?php
/**
 * Created by PhpStorm.
 * User: Informatico
 * Date: 14/7/2020
 * Time: 15:10
 */

namespace App\Repository;

use App\Entity\Lotjuridicas;
use App\Entity\Municipios;
use App\Entity\Provincias;
use App\Entity\Ramas;
use App\Entity\TipoServicio;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class LotjuridicasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lotjuridicas::class);
    }

    public function findAll()
    {
        $em = $this->getEntityManager();
        $consulta = $em->createQuery('
        SELECT l, pj,m,p,e,r,ex FROM App:Lotjuridicas l
        JOIN l.identidad pj
        JOIN pj.idmunicipio m
        JOIN m.provinciaid p
        JOIN l.idestado e
        JOIN l.idrama r
        JOIN l.idextension ex
        ');
        return $consulta->getResult();

       /* $qb = $this->createQueryBuilder('lj')
           // ->add("select l,pj from App:Lotjuridicas l JOIN l.identidad ");
            ->from("App:Lotjuridicas","l");
        $qb ->getQuery()
            ->getResult();
        return $qb;
       /* return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
       $qb->add('select', 'u')
       ->add('from', 'User u')
       ->add('orderBy', 'u.name ASC')
       ->setFirstResult( $offset )
       ->setMaxResults( $limit );
       */
    }
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        $em = $this->getEntityManager();
        $consulta = $em->createQuery('
        SELECT l, pj,m,p,e,r,ex,tp,s FROM App:Lotjuridicas l
        JOIN l.identidad pj
        JOIN pj.idmunicipio m
        JOIN m.provinciaid p
        JOIN l.idestado e
        JOIN l.idrama r
        JOIN l.idextension ex
        JOIN l.idtipo tp
        JOIN l.idservicio s
        where l.id = :num 
        ');
        $consulta->setParameter('num',$id);
        try{
            return $consulta->getSingleResult();
        }
        catch (NoResultException $exception){
            return null;
        }
    }

    public function getWithSearchQueryBuilder(User $user=null): QueryBuilder
    {
        if(!$user)
        return $this->createQueryBuilder('l')
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
        return $this->createQueryBuilder('l')
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
            ->andWhere('p.id = :u')
            ->setParameter('u',$user->getMunicipio()->getProvinciaid()->getId())
            ;
    }

    public function searchLot($search){
        return $this->createQueryBuilder('l')
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
            ->andWhere('l.id  like :val')
            ->setParameter('val','%'.$search.'%');
    }
    public function searchByPj($search){
        return $this->createQueryBuilder('l')
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
            ->andWhere('pj.nomentidad  like :val')
            ->setParameter('val','%'.$search.'%');
    }
    public function lotsdemiprov(Provincias $p){
        return $this->createQueryBuilder('l')
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
            ->andWhere('p =:val')
            ->setParameter('val',$p->getId());
    }
    public function lotsTramitadasbyUser(User $user){
        return $this->createQueryBuilder('l')
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
            ->leftJoin('l.idtramitador','trmdr')
            ->andWhere('trmdr =:val')
            ->setParameter('val',$user->getId());
    }
    public function lotsAprobbyUser(User $user){
        return $this->createQueryBuilder('l')
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
            ->leftJoin('l.idaprueba','aprueba')
            ->andWhere('aprueba =:val')
            ->setParameter('val',$user->getId());
    }
    public function lotsCanceladas(Provincias $prov){
        return $this->createQueryBuilder('l')
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
            ->leftJoin('l.idaprueba','aprueba')
            ->andWhere('p.id =:val and e.id=8')
            ->setParameter('val',$prov->getId());
//        $em = $this->getEntityManager();
//        $consulta = $em->createQuery('
//        SELECT l, pj,m,p,b FROM App:Lotjuridicas l
//        JOIN l.identidad pj
//        JOIN pj.idmunicipio m
//        JOIN m.provinciaid p
//        JOIN l.idestado e
//        JOIN l.basificaciones b
//        where (p.id = :num or (b.idmun in (select mun.id from App:Provincias prov inner join App:Municipios mun where prov.id = :num))) and e.id = 8
//        ');
//        $consulta = $em->createQuery('
//        SELECT l, pj,m,p,b FROM App:Lotjuridicas l
//        JOIN l.identidad pj
//        JOIN pj.idmunicipio m
//        JOIN m.provinciaid p
//        JOIN l.idestado e
//        JOIN l.basificaciones b
//        where (p.id <> :num and e.id = 8)
//        ');
//        $consulta = $em->createQuery('
//        SELECT l, pj,m,p,b FROM App:Lotjuridicas l
//        JOIN l.identidad pj
//        JOIN pj.idmunicipio m
//        JOIN m.provinciaid p
//        JOIN l.idestado e
//        JOIN l.basificaciones b
//        where p.id = :num and e.id = 8
//        ');
//        $consulta->setParameter('num',$prov->getId());
//        try{
//            return $consulta->getResult();
//        }
//        catch (NoResultException $exception){
//            return null;
//        }
    }
    public function lotsCanceladasConBasifenProv(Provincias $prov){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery('
        SELECT l, pj,m,p,b FROM App:Lotjuridicas l
        JOIN l.identidad pj
        JOIN pj.idmunicipio m
        JOIN m.provinciaid p
        JOIN l.idestado e
        JOIN l.basificaciones b
        where (p.id <> :num and e.id = 8)
        ');
        $consulta->setParameter('num',$prov->getId());
        return $consulta->getResult();
    }
    public function lotsVig(Ramas $rama, TipoServicio $ts){
        return $this->createQueryBuilder('l')
            ->leftJoin('l.identidad','pj')
            ->addSelect('pj')
            ->leftJoin('pj.idmunicipio','m')
            ->addSelect('m')
            ->leftJoin('m.provinciaid','p')
            ->addSelect('p')
            ->leftJoin('l.idestado','e')
            ->addSelect('e')
            ->leftJoin('l.idservicio','ts')
            ->addSelect('ts')
            ->leftJoin('l.idrama','r')
            ->addSelect('r')
            ->leftJoin('l.idextension','ex')
            ->addSelect('ex')
            ->leftJoin('l.basificaciones','b')
            ->addSelect('b')
            ->andWhere('r.id = :r')
            ->andWhere('e.id = 5')
            ->andWhere('ts.id = :ts')
            ->setParameter('r',$rama->getId())
            ->setParameter('ts',$ts->getId())
            ;

    }

    public function lotsXMcpio(Municipios $mcpio){
        return $this->createQueryBuilder('l')
            ->leftJoin('l.identidad','pj')
            ->addSelect('pj')
            ->leftJoin('pj.idmunicipio','m')
            ->addSelect('m')
            ->leftJoin('pj.idorga','o')
            ->addSelect('o')
            ->leftJoin('l.idservicio','s')
            ->addSelect('s')
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
            ->andWhere('m =:val')
            ->setParameter('val',$mcpio->getId());
    }
}