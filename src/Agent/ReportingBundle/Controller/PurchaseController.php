<?php

namespace AlAya\Agent\ReportingBundle\Controller;

use AlAya\Agent\CommonBundle\Attribute\Access;
use AlAya\Common\Entity\Purchase;
use AlAya\Common\Form\PurchaseFormType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/purchase')]
class PurchaseController extends AbstractController
{
    private EntityManagerInterface $manager;
    private PaginatorInterface $paginator;

    public function __construct(EntityManagerInterface $manager, PaginatorInterface $paginator)
    {
        $this->manager = $manager;
        $this->paginator = $paginator;
    }

    #[Route('/', name: 'back_purchase_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $query = $this->manager->getRepository(Purchase::class)->all($request->query);
        $purchases = $this->paginator->paginate($query, $request->query->getInt('page', 1), 20);

        $purchase = new Purchase();
        $form = $this->createForm(PurchaseFormType::class, $purchase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($purchase);
            $this->manager->flush();
            $this->addFlash('success', 'Achat ajouté avec succès.');
            return $this->redirectToRoute('back_purchase_index');
        }

        return $this->render('@AgentReportingBundle/purchase/index.twig', [
            'purchases' => $purchases,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'back_purchase_delete', methods: ['POST'])]
    public function delete(Request $request, Purchase $purchase): Response
    {
        if ($this->isCsrfTokenValid('delete_purchase_' . $purchase->getId(), $request->request->get('_token'))) {
            $this->manager->remove($purchase);
            $this->manager->flush();
            $this->addFlash('success', 'Achat supprimé avec succès.');
        }
        return $this->redirectToRoute('back_purchase_index');
    }
}
