<?php

namespace AlAya\Agent\ReportingBundle\Controller;

use AlAya\Common\Entity\Charge;
use AlAya\Common\Form\ChargeFormType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/charge')]
class ChargeController extends AbstractController
{
    private EntityManagerInterface $manager;
    private PaginatorInterface $paginator;

    public function __construct(EntityManagerInterface $manager, PaginatorInterface $paginator)
    {
        $this->manager = $manager;
        $this->paginator = $paginator;
    }

    #[Route('/', name: 'back_charge_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
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

        return $this->render('@AgentReportingBundle/charge/index.twig', [
            'charges' => $charges,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'back_charge_delete', methods: ['POST'])]
    public function delete(Request $request, Charge $charge): Response
    {
        if ($this->isCsrfTokenValid('delete_charge_' . $charge->getId(), $request->request->get('_token'))) {
            $this->manager->remove($charge);
            $this->manager->flush();
            $this->addFlash('success', 'Charge supprimée avec succès.');
        }
        return $this->redirectToRoute('back_charge_index');
    }
}
