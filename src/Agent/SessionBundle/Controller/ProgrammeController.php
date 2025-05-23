<?php

namespace AlAya\Agent\SessionBundle\Controller;
use AlAya\Agent\CommonBundle\Controller\Controller;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/programmes")]
class ProgrammeController extends Controller
{

    public $em;
    public $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->em = $this->doctrine->getManager();
    }

    #[Route("/",name:"back_programme_index")]
    public function index(): Response
    { 
       return $this->render('@AgentSessionBundle/Programme/index.html.twig');
    }

    
}

