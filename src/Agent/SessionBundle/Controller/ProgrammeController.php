<?php

namespace AlAya\Agent\SessionBundle\Controller;
use AlAya\Agent\CommonBundle\Controller\Controller;
use AlAya\Common\Entity\Programme;
use AlAya\Common\Controller\BaseController;
use AlAya\Common\Form\ProgrammeFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/programmes")]
class ProgrammeController extends BaseController
{

    #[Route("/",name:"back_programme_index")]
    #[Template("@AgentSessionBundle/Programme/index.html.twig")]
    public function index()
    { 
        $programme = new Programme();
        $formPro = $this->createForm(ProgrammeFormType::class,$programme)->handleRequest($this->request);
        if ($formPro->isSubmitted() and $formPro->isValid()) {
           $programme = $formPro->getData();
           $this->doctrine->persist($programme);
           $this->doctrine->flush();
           $this->addFlash("success","Programme ajouté avec succès");
           return $this->redirectToRoute("back_programme_index");
        }
       return [
            'programmes' => $this->repo(Programme::class)->findAll(),
            'form' => $formPro->createView(),
       ];
    }

    #[Route("/show/{programme}",name:"back_programme_show")]
    #[Template("@AgentSessionBundle/Programme/show.html.twig")]
    public function show(Programme $programme)
    { 
        $formPro = $this->createForm(ProgrammeFormType::class,$programme)->handleRequest($this->request);
        if ($formPro->isSubmitted() and $formPro->isValid()) {
           $programme = $formPro->getData();
           $this->doctrine->persist($programme);
           $this->doctrine->flush();
           $this->addFlash("success","Programme modifié avec succès");
           return $this->redirectToRoute("back_programme_index");
        }
       return [
            'form' => $formPro->createView()
       ];
    }
   

    
}

