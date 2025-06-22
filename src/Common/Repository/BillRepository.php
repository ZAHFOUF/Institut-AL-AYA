<?php

namespace AlAya\Common\Repository;

use AlAya\Common\Entity\Bill;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bill>
 */
class BillRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bill::class);
    }

    public function getUnPaidBills($student): array
    {
        return $this->createQueryBuilder('b')
             ->innerJoin('b.prestation', 'p')
             ->innerJoin('p.student', 's')
             ->andWhere("s.id = :studentId")
             ->andWhere("IFNULL(b.payed,0) = 0")
             ->setParameter('studentId', $student->getId())
             ->orderBy('p.id', 'DESC')
             ->getQuery()
             ->getResult();
    }


    //    /**
    //     * @return Bill[] Returns an array of Bill objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Bill
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
