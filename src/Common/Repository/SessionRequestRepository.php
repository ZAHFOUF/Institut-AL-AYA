<?php

namespace AlAya\Common\Repository;

use AlAya\Common\Entity\SessionRequest;
use AlAya\Common\Service\QueryParser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @extends ServiceEntityRepository<Session>
 */
class SessionRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry,private QueryParser $queryParser,private TokenStorageInterface $tokenStorage)
    {
        parent::__construct($registry, SessionRequest::class);
    }


    public function all(InputBag $inputBag)
    {
        $query = $this->createQueryBuilder('s') ;
        return $this->queryParser->filter($query,$inputBag)
            ->orderBy("s.submittedAt","DESC")
            ->getQuery()
            ->getResult();
    }

    public function getSessionRequestOfStudent()
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

//    /**
//     * @return Session[] Returns an array of Session objects
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

//    public function findOneBySomeField($value): ?Session
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
