<?php

namespace AlAya\Common\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use AlAya\Common\Service\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Request ;

abstract class BaseController extends AbstractController
{


    public EntityManagerInterface $doctrine;
    public Request $request;
    

    public function repo ($entity) {
        return $this->doctrine->getRepository($entity);
    }


}
