<?php

namespace AlAya\Common\Events;

use AlAya\Common\Entity\Session;
use AlAya\Common\Entity\SessionLine;
use AlAya\Common\Entity\SessionRequest;
use AlAya\Common\Service\RequestAutomation;
use AlAya\Common\Service\SessionService;
use Doctrine\Persistence\Event\LifecycleEventArgs ;
use Symfony\Component\BrowserKit\Request;

class SessionListener
{

    public function __construct(private SessionService $service,private RequestAutomation $automation)
    {
        
    }
    public function prePersist(LifecycleEventArgs $args): void
    {
        

    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        
        $object = $args->getObject() ;


         // Notification invitation séance
         if ($object instanceof SessionLine) {
            if ($object->getSession()->getStatus()->getId() == 2) {
                $this->service->onSessionLineCreated($object);
            }
            
        }

        if ($object instanceof Session) {
             $object->setLink(bin2hex(random_bytes(5)));
        }

        if ($object instanceof SessionRequest) {
            $this->automation->autoAffectation($object);
        }



    }
}
