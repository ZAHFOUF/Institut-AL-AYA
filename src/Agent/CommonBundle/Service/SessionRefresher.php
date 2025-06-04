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
        
   }

   public function basicRefresh(Session $session) 
   {
   }
    
}
