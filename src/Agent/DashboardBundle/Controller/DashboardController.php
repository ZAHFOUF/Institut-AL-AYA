<?php

namespace AlAya\Agent\DashboardBundle\Controller;

use AlAya\Agent\CommonBundle\Attribute\Access;
use AlAya\Agent\CommonBundle\Controller\Controller;
use AlAya\Common\Entity\Agent;
use AlAya\Common\Entity\SessionLine;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends Controller
{


    public $em;
    public $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->em = $this->doctrine->getManager();
    }

    #[Route("/",name:"AgentDashboardBundle_Index")]
    public function index(): Response
    { 
        $typeUser = $this->getUser()->getType()?->getId() ;
        if ($typeUser == 1) {return $this->redirectToRoute("back_agent_index");}
        return $this->render('@AgentDashboardBundle/index.html.twig');
    }

    #[Route("/mon-planing",name:"AgentDashboardBundle_MonPlaning")]
    public function monPlaning(): Response
    { 
        return $this->render('@AgentDashboardBundle/index.html.twig');
    }

    // The api
    #[Route(path:"/planing",name:"admin_planing",methods:["GET","POST"])]
    public function planing()  {
        $sessionLines = [];
        $sessions = $this->doctrine->getRepository(SessionLine::class)->getSessionOfAgent();
        foreach($sessions as $sessionLine){
            $sessionLines[] = [
                "title" => $sessionLine->getObjective() . " - " . $sessionLine->getTimeStart()?->format("H:i") . "|" . $sessionLine->getTimeEnd()?->format("H:i"),
                "date" => $sessionLine->getDate()->format("Y-m-d H:i:s"),
                "allDay" => false,
                "link" => $this->getParameter("meet.url") . $sessionLine->getSession()->getLink()
            ];
        }
        return new JsonResponse($sessionLines); 
    }
}
