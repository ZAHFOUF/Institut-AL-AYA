<?php

namespace AlAya\Common\Repository;

use AlAya\Common\Entity\Bill;
use AlAya\Common\Entity\Payement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Payement>
 */
class PayementRepository extends BaseRepository
{

    public function getBills($prestation) {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.bill', 'b')
            ->andWhere("b.prestation = :prestation")
            ->setParameter('prestation', $prestation)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
  

     protected function getEntityClass(): string
    {
        return Payement::class;
    
    }
}
