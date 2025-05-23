<?php

namespace AlAya\Common\Repository;

use AlAya\Common\Entity\Agent;
use AlAya\Common\Entity\Group;
use AlAya\Common\Entity\Student;
use AlAya\Common\Service\QueryParser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\InputBag;

/**
 * @extends ServiceEntityRepository<Group>
 */
class GroupRepository extends BaseRepository
{
   

    public function all(InputBag $inputBag)
    {
        $query = $this->createQueryBuilder('s') ;

        if ($this->getUser() instanceof Agent and $this->getUser()->getType()->getId() == 2) {
            $query->andWhere('s.teacher = :teacher')
                ->setParameter('teacher', $this->getUser()->getId());
        }

        return $this->queryParser->filter($query,$inputBag)
            ->orderBy("s.id","DESC")
            ->getQuery()
            ->getResult();
    }

    public function groupsForChat($user){
        if ($user instanceof Agent) {
            return $this->andWhere("g.teacher_id = :teacher")
                   ->addParam("teacher",$user->getId())
                   ->orderBy("g.id","DESC")
                   ->executeQuery("SELECT g.id , g.name , (
        SELECT COUNT(m.id) FROM message m WHERE m.groupe_id = g.id  AND NOT JSON_CONTAINS( IFNULL(m.viewers, '[]'), JSON_QUOTE(:teacher) )) AS newMsg FROM `group` g");
        }
        if ($user instanceof Student) {
            $id = $user->getId() ;
            return $this
            ->addParam("user",$id)
            ->andWhere('JSON_CONTAINS(g.students, "'.$id.'")' . ' OR JSON_CONTAINS(g.students, \'"'. $id . '"\')'  )
            ->orderBy("g.id", "DESC")
            ->executeQuery("SELECT g.id, g.name, (
        SELECT COUNT(m.id) FROM message m WHERE m.groupe_id = g.id  AND NOT JSON_CONTAINS( IFNULL(m.viewers, '[]'), JSON_QUOTE(:user) )) AS newMsg FROM `group` g");
        }
        return [] ;
    }

    public function groups () {
        $id = $this->getUser()->getId() ;
        return $this
            ->andWhere('JSON_CONTAINS(g.students, "'.$id.'")' . ' OR JSON_CONTAINS(g.students, \'"'. $id . '"\')'  )
        ->executeQuery("SELECT g.name,g.max,CONCAT(IFNULL(a.last_name,''),' ',IFNULL(a.first_name,'')) as 't',sg.name as 'g' ,g.invitation_uuid as inv
FROM `group` g
LEFT JOIN student_gender sg ON sg.id = g.gender_id
LEFT JOIN agent a ON a.id = g.teacher_id");
    }


//    /**
//     * @return Group[] Returns an array of Group objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Group
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

protected function getEntityClass(): string
    {
        return Group::class;
    }

}
