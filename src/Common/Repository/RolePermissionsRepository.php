<?php

namespace AlAya\Common\Repository;

use AlAya\Common\Entity\RolePermissions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RolePermissions>
 *
 * @method RolePermissions|null find($id, $lockMode = null, $lockVersion = null)
 * @method RolePermissions|null findOneBy(array $criteria, array $orderBy = null)
 * @method RolePermissions[]    findAll()
 * @method RolePermissions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RolePermissionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RolePermissions::class);
    }

    public function add(RolePermissions $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RolePermissions $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return RolePermissions[] Returns an array of RolePermissions objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RolePermissions
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

/**
    * @return RolePermissions[] Returns an array of RolePermissions objects
    */
    public function findByRole($role): array
    {
        return array_values( $this->createQueryBuilder('r')
        ->select("p.id")
        ->join("r.role","ro")
        ->join("r.permission","p")
            ->andWhere('ro.id = :role ')
            ->setParameter('role', $role)
            ->getQuery()
            ->getArrayResult())
        ;
    }
    
}