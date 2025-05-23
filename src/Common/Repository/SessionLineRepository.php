<?php

namespace AlAya\Common\Repository;

use AlAya\Common\Entity\SessionLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @extends ServiceEntityRepository<SessionLine>
 */
class SessionLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry,private TokenStorageInterface $tokenStorage)
    {
        parent::__construct($registry, SessionLine::class);
    }

    //    /**
    //     * @return SessionLine[] Returns an array of SessionLine objects
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

    //    public function findOneBySomeField($value): ?SessionLine
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function getSessionOfAgent()
    {
        $user = $this->tokenStorage->getToken()->getUser()->getId();
        return $this->createQueryBuilder('sl')
        ->distinct()
            ->join('sl.session', 's')
            ->where('s.teacher = :agent')
            ->andWhere("sl.status != :status")
            ->setParameter('status',2)
            ->setParameter('agent', $user)
            ->getQuery()
            ->getResult();
    }
}
