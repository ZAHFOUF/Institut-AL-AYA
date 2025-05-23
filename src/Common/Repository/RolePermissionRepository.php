<?php

namespace AlAya\Common\Repository;

use AlAya\Common\Entity\RolePermission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RolePermission>
 *
 * @method RolePermission|null find($id, $lockMode = null, $lockVersion = null)
 * @method RolePermission|null findOneBy(array $criteria, array $orderBy = null)
 * @method RolePermission[]    findAll()
 * @method RolePermission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RolePermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RolePermission::class);
    }

    public function add(RolePermission $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RolePermission $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return RolePermission[] Returns an array of RolePermission objects
     */
    public function findModules(): array
    {
        return $this->createQueryBuilder('r')
        ->select("r.module")
            ->distinct()
            ->groupBy("r.module")
            ->getQuery()
            ->getResult()
        ;
    }


//    /**
//     * @return RolePermission[] Returns an array of RolePermission objects
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

//    public function findOneBySomeField($value): ?RolePermission
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}