<?php

namespace AlAya\Agent\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class AuthenticationController extends AbstractController
{

    #[Route("/chat", name: "admin_chat", methods: ["GET"])]
     public function chat()
     {
             return $this->render('@AgentSecurityBundle/chat/chat.twig');  
    }

    #[Route("/login",name:"AgentSecurityBundle_Login")]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('AgentDashboardBundle_Index');
        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('@AgentSecurityBundle/Authentication/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route("/logout",name:"AgentSecurityBundle_LogOut")]
    public function logout()
    {
        
    }


}