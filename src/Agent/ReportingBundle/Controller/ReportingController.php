<?php

namespace AlAya\Agent\ReportingBundle\Controller;

use AlAya\Agent\CommonBundle\Attribute\Access;
use AlAya\Agent\CommonBundle\Controller\Controller ;
use AlAya\Common\Repository\AgentRepository;
use AlAya\Common\Repository\SessionRepository;
use AlAya\Common\Service\Export;
use AlAya\Common\Service\RequestGetter;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;


#[Route("/reporting")]
#[Access]
class ReportingController extends Controller
{

    public function __construct(private PaginatorInterface $paginator)
    {
    }

    #[Route('/rémunérations-professeurs', name: 'back_reporting_index', methods: ['GET','POST'])]
    public function indexAction(SessionRepository $sessionRepository,AgentRepository $agentRepository,Request $request,Export $export)
    {

        RequestGetter::initialize($request->query);

        !$request->query->has("year") ? $request->query->set("year",date("Y")) : 0 ;
        $data        = $sessionRepository->reportingIndex($request->query);

         // Export
         if (RequestGetter::isNotEmpty("export") and count($data) > 0) {
            return $export->toExcel($data, "Reporting rémunérations des professeurs");
        }

        $teachers    = $agentRepository->findBy(['type' => 2]);
        return $this->render('@AgentReportingBundle/index.twig',[
            'results' => $this->paginator->paginate($data, $request->query->getInt("page", 1), 10),
            'teachers' => $teachers
        ]);
    }
}
