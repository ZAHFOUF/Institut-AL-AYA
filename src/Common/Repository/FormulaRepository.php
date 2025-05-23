<?php

namespace AlAya\Common\Repository;

use AlAya\Common\Entity\Formula;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Formula>
 */
class FormulaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formula::class);
    }

       /**
        * @return Formula[] Returns an array of Formula objects
        */
       public function findByModuleAndType($module,$type): array
       {
           return $this->createQueryBuilder('f')
                ->select("f.id , f.hoursPerWeek , f.totalHours as hours , t.price, f.daysPerWeek , f.avgSession")
                ->andWhere('f.module = :module')
                ->join("f.module","m")->join("f.formulaSessionTypes","t","t.formula = f")
                ->andWhere('m.id = :module')
                ->andWhere('t.type = :type')
                ->setParameter('module', $module) 
                ->setParameter('type', $type)
                ->getQuery()
                ->getArrayResult()
           ;
       }

    //    public function findOneBySomeField($value): ?Formula
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
