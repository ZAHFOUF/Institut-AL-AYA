<?php

namespace AlAya\Common\Service ;

use AlAya\Common\Entity\Agent;
use AlAya\Common\Repository\AgentRepository;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class AuthChecker 
{

   public function __construct(private AgentRepository $agentRepository)
   {
    
   }


   public function checkPermission(string $name,Agent $agent) : bool
   {
       $permissions = $this->agentRepository->findPermissions($agent);
       return in_array($name,[...$permissions]);  
   }

   public function denyIfNoPermission(string $name,Agent $agent)  {
        if (!$this->checkPermission($name,$agent)) {
            throw new AccessDeniedException("Access denited !") ;
        }
   }
    
}
