<?php

namespace AlAya\Common\Repository;

use AlAya\Common\Entity\Prestation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Prestation>
 */
class PrestationRepository extends BaseRepository
{
  

    public function getUnPaidBills($student): array
    {
        return $this->createQueryBuilder('p')
             ->innerJoin('p.student', 's')
             ->andWhere("s.id = :studentId")
             ->andWhere("p.status = :status ")
             ->setParameter('status','en attendant le paiement')
             ->setParameter('studentId', $student->getId())
             ->orderBy('p.id', 'DESC')
             ->getQuery()
             ->getResult();
    }

    public function all()  {
        $user = $this->getUser();
        if ($user->getType()->getId() == 1) {
            return $this->createQueryBuilder('p')
                ->orderBy('p.id', 'DESC')
                ->getQuery()
                ->getResult();
        }

        return $this->createQueryBuilder('p')
            ->where("p.agent = :agent")
            ->setParameter('agent', $user)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
        
    }

//    /**
//     * @return Prestation[] Returns an array of Prestation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Prestation
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

  protected function getEntityClass(): string
    {
        return Prestation::class;
    
    }
}
