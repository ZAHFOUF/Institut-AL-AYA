<?php

namespace AlAya\Agent\PrestationBundle\Controller;

use AlAya\Agent\CommonBundle\Attribute\Access;
use AlAya\Agent\PrestationBundle\Form\PayementAddType as FormPayementAddType;
use AlAya\Common\Controller\BaseController;
use AlAya\Common\Entity\Payement;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Attribute\Route as AttributeRoute;

class PayementController extends BaseController
{
    #[AttributeRoute('/payements', name: 'payement_index')]
    #[Template("@AgentPrestationBundle/Payement/index.html.twig")]
    #[Access()]
    public function index()
    {
        $payements = $this->repo(Payement::class)->findAll();

        $payement = new Payement();

        $form = $this->createForm(FormPayementAddType::class, $payement,['prestation' => true]);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->doctrine->persist($payement);
            $this->doctrine->flush();

            return $this->redirectToRoute('payement_index');
        }

        return [
            'payements' => $payements,
            'form' => $form->createView()
        ];
    }

}