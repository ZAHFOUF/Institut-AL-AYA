<?php

// src/Common/EventSubscriber/LocaleSubscriber.php

namespace AlAya\Common\EventSubscriber;

use AlAya\Agent\CommonBundle\Attribute\Access;
use AlAya\Agent\CommonBundle\Service\AuthChecker;
use ReflectionClass;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Attribute\Route;

class LocaleSubscriber implements EventSubscriberInterface
{
   
    private $authChecker;
    private $security;
    

    public function __construct(AuthChecker $authChecker,Security $security)
    {
        $this->authChecker = $authChecker ;
        $this->security = $security ;
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.controller' => 'onKernelController'
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();
        
        // When a controller is defined as a service, it's received as an array (service, method)
        if (is_array($controller)) {
            $controller = new \ReflectionMethod($controller[0], $controller[1]);
            $attributes = $controller->getAttributes(Access::class);

            if (empty($attributes)) {
                $class = new \ReflectionClass( $event->getController()[0]) ;
                $attributes = $class->getAttributes(Access::class) ;
            }

            if (!empty($attributes)) {
                $route = $controller->getAttributes(Route::class)[0]->newInstance()->getName() ;
                $attribute = $attributes[0]->newInstance();
                $permission = $attribute->getName() ?? $route ;
                if (!$this->authChecker->checkPermission($permission,$this->security->getUser())) {
                        throw new AccessDeniedException("Access denited !") ;
                }
            }
    
         
    
        }
       
    }

}
