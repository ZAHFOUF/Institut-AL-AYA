<?php

namespace AlAya\Agent\CommonBundle\Service ;

use AlAya\Common\Entity\Agent;
use AlAya\Common\Repository\AgentRepository;

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
    
}
