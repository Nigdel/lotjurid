<?php

namespace App\Repository;

use App\Entity\DireccionProvincial;
use App\Entity\OficinaMcpal;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

     /**
      * @return User[] Returns an array of User objects
      * @param OficinaMcpal
      */

    public function findFuncToOM($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.municipio = :val and (u.oficinaMcpal is null or u.oficinaMcpal !=:val2)')
            ->setParameter('val', $value->getMunicipio())
            ->setParameter('val2',$value->getId())
            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    /**
     * @return User[] Returns an array of User objects
     * @param DireccionProvincial
     */
    public function findFuncToDP($value)
    {
//
//        $conn = $this->getEntityManager()->getConnection();
//
//        $sql = '
//        SELECT * FROM user u inner join Municipios m on u.municipio = m.id
//        WHERE u.municipio > :price
//        ORDER BY p.price ASC
//        ';
//        $stmt = $conn->prepare($sql);
//        $stmt->execute(['price' => $price]);
//
//        // returns an array of arrays (i.e. a raw data set)
//        return $stmt->fetchAll();
//
        return $this->createQueryBuilder('u')
            ->innerJoin('u.municipio','m')
            ->innerJoin('m.provinciaid','p')
            ->andWhere('p = :val and (u.direccionProvincial is null or u.direccionProvincial !=:val2)')
            ->andWhere('u.oficinaMcpal is null')
            ->setParameter('val', $value->getProvincia())
            ->setParameter('val2',$value->getId())
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
