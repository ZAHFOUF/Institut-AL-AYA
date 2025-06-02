<?php

namespace AlAya\Student\SecurityBundle\Controller;

use AlAya\Common\Entity\Country;
use AlAya\Common\Entity\Formula;
use AlAya\Common\Entity\Group;
use AlAya\Common\Entity\GroupRequestType;
use AlAya\Common\Entity\Language;
use AlAya\Common\Entity\Module;
use AlAya\Common\Entity\Session;
use AlAya\Common\Entity\SessionGroup;
use AlAya\Common\Entity\SessionRequest;
use AlAya\Common\Entity\SessionRequestStatus;
use AlAya\Common\Entity\SessionStatus;
use AlAya\Common\Entity\SessionStudent;
use AlAya\Common\Entity\SessionType;
use AlAya\Common\Entity\Student;
use AlAya\Common\Entity\StudentGender;
use AlAya\Common\Entity\StudentLanguage;
use AlAya\Common\Repository\FormulaRepository;
use AlAya\Common\Repository\MessageRepository;
use AlAya\Common\Repository\SessionRepository;
use AlAya\Common\Service\Notification;
use AlAya\Common\Service\RequestAutomation;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Pusher\Pusher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class AuthenticationController extends AbstractController
{

    public $doctrine ;
    public $sessionRepository ;
    public $studentRepository ;

    function __construct(EntityManagerInterface $m)
    {
        $this->doctrine = $m;
        $this->sessionRepository = $m->getRepository(Session::class);
        $this->studentRepository = $m->getRepository(Student::class);
    }

    #[Route("/login",name:"tech_student_login")]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render("@StudentSecurityBundle/login.twig",["error" => $error,"lastUsername" => $lastUsername]);
    }

    #[Route("/logout",name:"tech_register_logout")]
    public function logout()
    {
        
    }


}