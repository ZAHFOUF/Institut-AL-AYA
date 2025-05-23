<?php

namespace AlAya\Common\Repository;

use AlAya\Common\Entity\Agent;
use AlAya\Common\Service\QueryParser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Agent>
 *
 * @method Agent|null find($id, $lockMode = null, $lockVersion = null)
 * @method Agent|null findOneBy(array $criteria, array $orderBy = null)
 * @method Agent[]    findAll()
 * @method Agent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgentRepository extends BaseRepository implements PasswordUpgraderInterface
{

    public function save(Agent $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Agent $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Agent) {
            throw new UnsupportedUserException(sprintf('Les instances de "%s" ne sont pas prises en charge.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }


    public function search($params)
    {

        $query = $this->createQueryBuilder('a');
        $this->queryParser->filter($query, $params);

        return $query->getQuery()->getResult();
    }

    public function mainAlgo ($aviaability,$days,$timezone){
        
    }

    public function findAvailableAgents(array $availabilityList, array $daysList,int $module,int $gender)
{
    $qb = $this->createQueryBuilder('a');

    return $qb
        ->where('a.type = :type')
        ->andWhere('a.dispo = 1')
        ->andWhere('a.deleted IS NULL OR a.deleted != 1')
        ->andWhere('JSON_OVERLAPS(a.availability, :availability) = 1')
        ->andWhere("JSON_OVERLAPS(a.days, :days) = 1 OR JSON_CONTAINS(a.days, JSON_QUOTE(:allDays)) = 1 ")
        ->andWhere("a.module is null or a.module = :module")
        ->andWhere("a.gender = :gender")
        ->setParameter('module', $module)
        ->setParameter("gender",$gender)
        ->setParameter('type', 2)
        ->setParameter('availability', json_encode($availabilityList))
        ->setParameter("days",json_encode($daysList))
        ->setParameter("allDays",'All')
        ->getQuery()
        ->getResult() ;
}


   public function checkPlanining($agent,$dateStart,$dateEnd){
             return  
                
                $this->addParam("dateStart", $dateStart)
                     ->addParam("dateEnd", $dateEnd)
                     ->addParam("agent", $agent->getId())
                     ->andWhere("a.id = :agent AND sl.date BETWEEN :dateStart AND :dateEnd")
                     ->groupBy("sl.date")
                     ->executeQuery("SELECT SUM(sl.hours) as total , sl.date  FROM `session_line` sl 
                                     INNER JOIN session s ON s.id = sl.session_id
                                     INNER JOIN agent a ON a.id = s.teacher_id
                                      ") ;
   }


//    /**
//     * @return Agent[] Returns an array of Agent objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Agent
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


     /**
     * 
     * @todo Returns the permissions of the current user ( session approach ) ( One Query No QueryBuilder )
     * 
     * @param  Agent $agent
     * @return array[] Returns an array of Permissions
     */

     public function findPermissions(Agent $agent)
     {

        $session = $this->request->getSession() ;
        if (is_null($session->get("permissions"))) {
            $id = $agent->getId();
            $sql = "select distinct(rp.name)  from role_permission rp 
            where id in (select rps.permission_id from role_permissions rps 
            where rps.role_id in (select role_id from role_agent ra where ra.agent_id = $id ) );" ;

            $conn = $this->getEntityManager()->getConnection();
            $stmt = $conn->prepare($sql);

            $execute = $stmt->executeQuery();

            $permissions= array_column( $execute->fetchAllAssociative() , "name" ) ;

            $session->set("permissions",$permissions);
            
            return $permissions;
        }

        

      return $session->get("permissions") ;

    }

    protected function getEntityClass(): string
    {
        return Agent::class;
    }



}
