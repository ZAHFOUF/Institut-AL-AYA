<?php

namespace AlAya\Common\Repository;

use AlAya\Common\Entity\Student;
use AlAya\Common\Service\QueryParser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Student>
 */
class StudentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry,private QueryParser $queryParser)
    {
        parent::__construct($registry, Student::class);
    }

    public function search($params)
    {
        $query = $this->createQueryBuilder('a');
        $this->queryParser->filter($query, $params);

        return $query->getQuery()->getResult();
    }

    public function studentsGroup($gender)  {
        return $this->createQueryBuilder("r")
        ->distinct()
        ->select("r.id,CONCAT(r.lastName,' ',r.firstName,' (',g.name,')') as name")
        ->join("r.gender","g")
        ->andWhere("g.id = :gender")
        ->setParameter("gender",$gender)
        ->getQuery()->getArrayResult();
    }

    //    /**
    //     * @return Student[] Returns an array of Student objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Student
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
