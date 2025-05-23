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

    #[Route("/register",name:"tech_student_register")]
    public function register(Request $request,Notification $notification,UserPasswordHasherInterface $passwordHasher,RequestAutomation $automation): Response
    {

        if ($request->isMethod("POST")) {
            try {
                $dataStudent = $request->request->all("student");
                $dataSession = $request->request->all("session");
    
                /** Student creation */
                $student = new Student();
                $student->setLastName($dataStudent["lastName"]);
                $student->setFirstName($dataStudent["firstName"]);
                $student->setEmail($dataStudent["email"]);
                $student->setAge($dataStudent["age"]);
                $student->setPhone($dataStudent["phone"]);
                $student->isAcceptTerms(boolval($dataStudent["acceptTerms"]));
                $student->setCountry($this->doctrine->getRepository(Country::class)->find($dataStudent["country"]));
                $student->setCity($dataStudent["city"]);
                $student->setGender($this->doctrine->getRepository(StudentGender::class)->find($dataStudent["gender"]));
                $student->setTimezone($dataSession["timezone"]);
                $student->setFirst(true);
                foreach ($dataStudent["langue"] ?? [] as $langue) {
                    $studentLangue = new StudentLanguage ;
                    $studentLangue->setStudent($student)->setLanguage($this->doctrine->getRepository(Language::class)->find($langue));
                    $this->doctrine->persist($studentLangue);
                }
                $this->doctrine->persist($student);
    
                /** Session creation */
                $formulas = $this->doctrine->getRepository(Formula::class)->find($dataSession["formula"]) ;
                /** @var  SessionRequest $session */
                $session = new SessionRequest();
                $session->setModule($this->doctrine->getRepository(Module::class)->find($dataSession["module"]));
                $session->setType($this->doctrine->getRepository(SessionType::class)->find($dataSession["type"]));
                $session->setFormula($formulas);
                $session->setAvailability($dataSession["availability"]);
                $session->setDays($dataSession["days"]);
                $session->setHours($formulas->getTotalHours() + (int)$dataSession["additionalHours"]);
                $session->setAdditionalHours((int)$dataSession["additionalHours"]);
                $session->setTimezone($dataSession["timezone"] ?? "");
                $session->setDateStart(new \DateTime($dataSession["dateStart"]));
                $session->setStatus($this->doctrine->getRepository(SessionRequestStatus::class)->find(1));
                $session->setStudent($student);
                $student->setPassword($passwordHasher->hashPassword($student, $dataStudent["password"]["first"]));
                if (isset($dataSession["groupType"])) { $session->setGroupType($this->doctrine->getRepository(GroupRequestType::class)->find($dataSession["groupType"]));}
                if (isset($dataSession["maxStudent"])) { $session->setMaxStudent((int)$dataSession["maxStudent"]);}
               
                $this->doctrine->persist($session);
    
                $this->doctrine->flush();
                $notification->sendNotificationEmail($student->getEmail(),"Bienvenue sur Institut Al-Ayah","@CommonTemplate/emails/welcome.twig",[
                    'password' => $dataStudent["password"]["first"],
                    'link' => $session?->getClasse()?->getInvitationUuid(),
                    'host' => $this->getParameter("project.host")
                ]) ;
                return $this->json(["status" => "success"]);
    
            } catch (\Throwable $th) {
                throw new Exception($th->getMessage());
                
            }
                       
        }

        $country = $this->doctrine->getRepository(Country::class)->findAll();
        $genders = $this->doctrine->getRepository(StudentGender::class)->findAll();
        $langues = $this->doctrine->getRepository(Language::class)->findAll();


        return $this->render("@StudentSecurityBundle/register.twig",[
            'country' => $country ,
            'genders' => $genders ,
            'langues' => $langues
        ]);
    }

    #[Route("/api/loadFormulats",name:"tech_student_loadFormulats")]
    public function loadFormulats(Request $request,FormulaRepository $formulaRepository): Response
    {
        $module = $request->query->get("module");
        $type = $request->query->get("type");
        $formulas = $formulaRepository->findByModuleAndType($module,$type);
        return $this->json($formulas);
    }


    #[Route("/api/loadGroups",name:"S_loadGroups")]
    #[Route("/admin/api/loadGroups",name:"A_loadGroups")]
    public function loadGroups(Request $request): Response
    {
        $user = $this->getUser() ;
        $groups = $this->doctrine->getRepository(Group::class)->groupsForChat($user);
        return $this->json($groups);
    }


    #[Route("/api/loadMessages",name:"S_loadMessages")]
    #[Route("/admin/api/loadMessages",name:"A_loadMessages")]
    public function loadMessages(Request $request,MessageRepository $repo): Response
    {
        $id = $request->query->get("group");
        $messages = $repo->group($id);
        return $this->json($messages);
    }

    #[Route("/api/addMessage", name:"S_addMessage", methods:["POST"])]
    #[Route("/admin/api/addMessage",name:"A_addMessage")]
    public function addMessage(Request $request, MessageRepository $repo,): Response
    {
        $message = json_decode($request->getContent());
        $object = $repo->addMessage($message);
        $options = array(
            'cluster' => 'eu',
            'useTLS' => true
          );
          $pusher = new Pusher(
            'fc6f94526d799ad819aa',
            'bb5169f8303ce85973b8',
            '1978097',
            $options
          );
        $pusher->trigger((string)$message->group, 'message', [
            'centent' => $message->centent,
            'userId' => $message->sender,
            'user' => $message->senderName ,
            'id' => $object->getId(),
            'time' => $object->getSendedAt()->format('H:i'),
            'date' => $object->getSendedAt()->format('Y-m-d ')
        ]);
        return $this->json(["status" => "success"]);
    }

    #[Route("/api/markAsRead", name:"S_markAsRead")]
    #[Route("/admin/api/markAsRead", name:"A_markAsRead")]
    public function markAsRead(Request $request, MessageRepository $repo): Response
    {

        $groupId = $request->query->get("group");
        $userId = $this->getUser()->getId();

        if (!$groupId) {
            return $this->json(["status" => "error", "message" => "Group ID is required"], Response::HTTP_BAD_REQUEST);
        }

        $repo->markAsRead($userId,$groupId);

        return $this->json(["status" => "success"]);
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