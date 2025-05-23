<?php

namespace AlAya\Common\Repository;

use AlAya\Common\Entity\SessionStudentFiles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @extends ServiceEntityRepository<SessionStudentFiles>
 */
class SessionStudentFilesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry,private TokenStorageInterface $tokenStorage)
    {
        parent::__construct($registry, SessionStudentFiles::class);
    }

     /**
        * @return SessionStudentFiles[] Returns an array of SessionStudentFiles objects
        */
        public function justifsOfStudents($session): array
        {
            return $this->createQueryBuilder('s')
                ->join("s.sessionStudent","g")
                ->join("g.session","se")
                ->andWhere('se.session = :session')
                ->setParameter('session', $session)
                ->getQuery()
                ->getResult()
            ;
        }

        /**
        * @return SessionStudentFiles[] Returns an array of SessionStudentFiles objects
        */
        public function justifsOfStudent(): array
        {
            $user = $this->tokenStorage->getToken()->getUser()->getId();
            return $this->createQueryBuilder('s')
                ->join("s.sessionStudent","g")
                ->join("g.student","se")
                ->andWhere('se.id = :student')
                ->setParameter('student', $user)
                ->getQuery()
                ->getResult()
            ;
        }

    //    /**
    //     * @return SessionStudentFiles[] Returns an array of SessionStudentFiles objects
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

    //    public function findOneBySomeField($value): ?SessionStudentFiles
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
