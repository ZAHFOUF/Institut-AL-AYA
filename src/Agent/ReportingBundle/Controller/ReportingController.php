<?php

namespace AlAya\Agent\ReportingBundle\Controller;

use AlAya\Agent\CommonBundle\Attribute\Access;
use AlAya\Agent\CommonBundle\Controller\Controller ;
use AlAya\Common\Entity\Charge;
use AlAya\Common\Form\ChargeFormType;
use AlAya\Common\Repository\AgentRepository;
use AlAya\Common\Repository\SessionRepository;
use AlAya\Common\Service\Export;
use AlAya\Common\Service\RequestGetter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


#[Route("/reporting")]
#[Access]
class ReportingController extends AbstractController
{

    public function __construct(private PaginatorInterface $paginator,private SessionRepository $sessionRepository,public EntityManagerInterface $manager)
    {
    }

    #[Route('/rémunérations-professeurs', name: 'back_reporting_index', methods: ['GET','POST'])]
    public function indexAction(AgentRepository $agentRepository,Request $request,Export $export)
    {

        RequestGetter::initialize($request->query);

        !$request->query->has("year") ? $request->query->set("year",date("Y")) : 0 ;
        !$request->query->has("month") ? $request->query->set("month",date("m")) : 0 ;
        $data        = $this->sessionRepository->reportingIndex($request->query);

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

    #[Route('/comptabilité', name: 'back_ca_index', methods: ['GET','POST'])]
    public function ca (Request $request,Export $export) {
         // Get the data
        $ca = $this->sessionRepository->ca($request->query);
         RequestGetter::initialize($request->query);

         // Export
         if (RequestGetter::isNotEmpty("export") and count($ca) > 0) {
            return $export->toExcel($ca, "Reporting chiffre d'affaires");
        }

        $query = $this->manager->getRepository(Charge::class)->createQueryBuilder('c')->orderBy('c.id', 'DESC')->getQuery();
        $charges = $this->paginator->paginate($query, $request->query->getInt('page', 1), 20);

        $charge = new Charge();
        $form = $this->createForm(ChargeFormType::class, $charge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($charge);
            $this->manager->flush();
            $this->addFlash('success', 'Charge ajoutée avec succès.');
            return $this->redirectToRoute('back_charge_index');
        }


        return $this->render('@AgentReportingBundle/ca.twig',
            [
                'results' => $this->paginator->paginate($ca, $request->query->getInt("page", 1), 10),
                'charges' => $charges,
                'form' => $form->createView(),
            ]
        );
    }


    #[Route('/delete/{id}', name: 'back_charge_delete', methods: ['POST'])]
    public function deleteCharge(Request $request, Charge $charge)
    {
        if ($this->isCsrfTokenValid('delete_charge_' . $charge->getId(), $request->request->get('_token'))) {
            $this->manager->remove($charge);
            $this->manager->flush();
            $this->addFlash('success', 'Charge supprimée avec succès.');
        }
        return $this->redirectToRoute('back_charge_index');
    }

}
