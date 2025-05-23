<?php

namespace AlAya\Common\Repository;

use AlAya\Common\Entity\SessionStudent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
/**
 * @extends ServiceEntityRepository<SessionStudent>
 */
class SessionStudentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry,private TokenStorageInterface $tokenStorage)
    {
        parent::__construct($registry, SessionStudent::class);
    }

       /**
        * @return SessionStudent[] Returns an array of SessionStudent objects
        */
       public function internsOfSession($session): array
       {
           return $this->createQueryBuilder('s')
               ->join("s.session","g")
               ->join("g.session","se")
               ->andWhere('se.id = :session')
               ->setParameter('session', $session)
               ->orderBy('s.createdAt', 'ASC')
               ->getQuery()
               ->getResult()
           ;
       }

    public function getSessionOfStudent()
    {
        $user = $this->tokenStorage->getToken()->getUser()->getId();
        return $this->createQueryBuilder('s')
        ->distinct()
            ->join('s.student', 'st')
            ->andWhere('st.id = :studentId')
            ->setParameter('studentId', $user)
            ->getQuery()
            ->getResult();
    }

    //    public function findOneBySomeField($value): ?SessionStudent
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
