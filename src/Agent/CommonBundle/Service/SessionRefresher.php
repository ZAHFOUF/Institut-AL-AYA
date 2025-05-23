<?php

namespace AlAya\Agent\CommonBundle\Service ;

use AlAya\Common\Entity\Group;
use AlAya\Common\Entity\Session;
use AlAya\Common\Entity\SessionGroup;
use AlAya\Common\Repository\SessionRepository;
use AlAya\Common\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;

class SessionRefresher
{

   public function __construct(private StudentRepository $studentRepository,private EntityManagerInterface $entityManagerInterface)
   {
    
   }


    /**
    * Automatic addition to started sessions
    */

   public function autoRefresh(Group $group,array $list = []) 
   {
        $activeSessions = $this->entityManagerInterface->getRepository(SessionGroup::class)->findBy([ 'group' => $group->getId() ]);
        foreach ($activeSessions as $gs) {
            if ($gs->getSession()->getStatus()->getId() == 2) {
                $session = $gs ;
                foreach ($list as $l) {
                    $relation = $session->addSessionStudent($this->studentRepository->find($l));
                    $this->entityManagerInterface->persist($relation);
                    $this->entityManagerInterface->persist($session);
                }
            }
        }
        $this->entityManagerInterface->flush();
   }

   public function basicRefresh(Session $session) 
   {
        $list  = $session->getSessionGroups() ;
        foreach ($list as $l) {
            /** @var Group $group */
            $group  =  $this->entityManagerInterface->getRepository(Group::class)->find($l->getGroup());
            foreach ($group->getStudents() as $s) {
                $relation = $l->addSessionStudent($this->studentRepository->find($s));
                $this->entityManagerInterface->persist($relation);
                $this->entityManagerInterface->persist($session);
            }
        }
        $this->entityManagerInterface->flush();
   }
    
}
