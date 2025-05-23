<?php

namespace AlAya\Common\Repository;

use AlAya\Common\Entity\Agent;
use AlAya\Common\Entity\Group;
use AlAya\Common\Entity\Message;
use AlAya\Common\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use stdClass;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends BaseRepository
{

    public function group($id){
        return $this->andWhere("m.groupe_id = :groupe")->addParam("groupe",$id)
        ->executeQuery("
              SELECT m.id , CONCAT(m.sender_type,'_',m.sender_id) as userId , 
              (CASE WHEN m.sender_type = 'S' THEN (SELECT CONCAT(s.last_name,' ',s.first_name) FROM student s WHERE s.id = m.sender_id) 
              WHEN m.sender_type = 'A' THEN (SELECT CONCAT(a.last_name,' ',a.first_name) FROM agent a WHERE a.id = m.sender_id) ELSE '' END ) as user , 
              DATE_FORMAT(m.sended_at, '%H:%i') as time , DATE(m.sended_at) as date , m.centent 
              FROM `message` m 
        ");
    }

    public function addMessage (stdClass $message)
    {
        $message->sender_type = explode('_', $message->sender)[0];
        $message->sender_id = explode('_', $message->sender)[1];
        $message->centent = htmlspecialchars($message->centent, ENT_QUOTES, 'UTF-8');
        $object = (new Message())
        ->setGroupe($this->getEntityManager()->getRepository(Group::class)->find($message->group))
        ->setSenderType($message->sender_type)
        ->setSenderId($message->sender_id)
        ->setCentent($message->centent)
        ->setViewers([(string)$this->getUser()->getId()])
        ->setSendedAt(new \DateTimeImmutable());
        $this->getEntityManager()->persist($object);
        $this->getEntityManager()->flush();
        return $object ;
    }
   

    public function markAsRead($userId,$groupId)
    {
        return $this->addParam("groupe",$groupId)
        ->addParam("user",$userId)
        ->executeQuery("UPDATE message SET viewers = JSON_ARRAY_APPEND(IFNULL(viewers, '[]'), '$', :user) WHERE groupe_id = :groupe AND NOT JSON_CONTAINS( IFNULL(viewers, '[]'), JSON_QUOTE(:user) )");
    }


    public function unReadMsg($user) {
        if ($user instanceof Agent) {
            $teacherId = $user->getId();
        
            $result = $this->addParam("teacher", $teacherId)
                ->executeQuery("
                    SELECT SUM(
                        (
                            SELECT COUNT(m.id) 
                            FROM message m 
                            WHERE m.groupe_id = g.id 
                              AND NOT JSON_CONTAINS(IFNULL(m.viewers, '[]'), JSON_QUOTE(:teacher))
                        )
                    ) AS totalUnread
                    FROM `group` g
                    WHERE g.teacher_id = :teacher
                ");
        
            return (int) ($result[0]['totalUnread'] ?? 0);
        }
        
        if ($user instanceof Student) {
            $id = $user->getId();
        
            $result = $this
                ->addParam('user_json', json_encode((string)$id))
                ->addParam("user", $id)
                ->executeQuery("
                    SELECT SUM(
                        (
                            SELECT COUNT(m.id) 
                            FROM message m 
                            WHERE m.groupe_id = g.id 
                              AND NOT JSON_CONTAINS(IFNULL(m.viewers, '[]'), JSON_QUOTE(:user))
                        )
                    ) AS totalUnread
                    FROM `group` g
                    WHERE JSON_CONTAINS(g.students, :user_json)
                ");
        
            return (int) ($result[0]['totalUnread'] ?? 0);
        }
        
        return 0;
        
    }
    
    

    //    /**
    //     * @return Message[] Returns an array of Message objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Message
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    protected function getEntityClass(): string
    {
        return Message::class;
    }
}
