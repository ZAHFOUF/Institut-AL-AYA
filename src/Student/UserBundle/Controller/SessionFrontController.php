<?php

namespace AlAya\Student\UserBundle\Controller;

use AlAya\Common\Entity\Country;
use AlAya\Common\Entity\Formula;
use AlAya\Common\Entity\Group;
use AlAya\Common\Entity\Language;
use AlAya\Common\Entity\Module;
use AlAya\Common\Entity\Prestation;
use AlAya\Common\Entity\SessionLine;
use AlAya\Common\Entity\SessionRequest;
use AlAya\Common\Entity\SessionRequestStatus;
use AlAya\Common\Entity\SessionStudent;
use AlAya\Common\Entity\SessionStudentFiles;
use AlAya\Common\Entity\SessionType;
use AlAya\Common\Entity\StudentGender;
use AlAya\Common\Form\SessionStudentFilesFromType;
use AlAya\Common\Service\FileManager;
use AlAya\Common\Service\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use AlAya\Common\Service\StudentRefresher;
use Symfony\Bridge\Twig\Attribute\Template;

class SessionFrontController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManager)
    {
        
    }

 /*   #[Route('/', name: 'student_sessions', methods: ['GET'])]
    public function sessions()
    {
        $sessions = $this->entityManager->getRepository(SessionStudent::class)->getSessionOfStudent();
        $groups = $this->entityManager->getRepository(Group::class)->groups ();
        return $this->render('@StudentUserBundle/session.twig',[
            'sessions' => $sessions,
            'groups' => $groups
        ]);
    }

    #[Route('/sessions-request', name: 'student_sessions_request', methods: ['GET'])]
    public function sessionsRequest()
    {
        $sessions = $this->entityManager->getRepository(SessionRequest::class)->getSessionRequestOfStudent();
        return $this->render('@StudentUserBundle/sessionRequest.twig',[
            'sessions' => $sessions
        ]);
    }

    #[Route('/profile', name: 'student_profile', methods: ['GET','POST'])]
    public function profile(StudentRefresher $studentRefresher,Request $request)
    {
        if($request->isMethod("POST") && $request->request->has("student")){
            $data = $request->request->all("student");
            $data["first"] = $this->getUser()->isFirst();
            $data["acceptTerms"] = $this->getUser()->isAcceptTerms();
            $data["timezone"] = $this->getUser()->getTimezone();
            $student = $studentRefresher->refreshStudent($this->getUser(),$data);
            $this->entityManager->persist($student);
            $this->entityManager->flush();
            $this->addFlash("success","Votre profile a été mis à jour avec succès");
            return $this->redirectToRoute("student_profile");
        }
        if($request->isMethod("POST") && $request->request->has("password_form")){
            $data = $request->request->all("password_form");
            $student = $studentRefresher->refreshPassword($this->getUser(),$data);
            $this->entityManager->persist($student);
            $this->entityManager->flush();
            $this->addFlash("success","Votre mot de passe a été mis à jour avec succès");
            return $this->redirectToRoute("student_profile");
        }
        $country = $this->entityManager->getRepository(Country::class)->findAll();
        $genders = $this->entityManager->getRepository(StudentGender::class)->findAll();
        $langues = $this->entityManager->getRepository(Language::class)->findAll();
        return $this->render('@StudentUserBundle/profile.twig',[
            'country' => $country,
            'genders' => $genders,
            'langues' => $langues
        ]);
    }

    #[Route('/payements', name: 'student_payements', methods: ["POST",'GET'])]
    public function payements(Request $request,FileManager $fileManager)
    {
        $sessions = $this->entityManager->getRepository(SessionStudent::class)->getSessionOfStudent();
        $files = $this->entityManager->getRepository(SessionStudentFiles::class)->justifsOfStudent();
        $sessionFiles = new SessionStudentFiles ;
        $sessionFiles->setFile(NULL);
        $sessionFilesForm = $this->createForm(SessionStudentFilesFromType::class,$sessionFiles,['student' => $this->getUser()->getId()]);

        $sessionFilesForm->handleRequest($request);

        if ($sessionFilesForm->isSubmitted() && $sessionFilesForm->isValid()) {
            /** @var SessionStudentFiles $sessionFiles */
    /*         $sessionFiles = $sessionFilesForm->getData();
             $this->entityManager->persist($sessionFiles);
             $this->entityManager->flush();
             $file = $request->files->get("justif");
             $sessionFiles->setFile($fileManager->upload($file,$this->getParameter("files.justifs") . "/" .  $sessionFiles->getId()));
             $this->entityManager->persist($sessionFiles);
             $this->entityManager->flush();
             return $this->redirect($request->headers->get("referer"));
        }

        return $this->render('@StudentUserBundle/payements.twig',[
            'files' => $files ,
            'sessionFilesForm' => $sessionFilesForm->createView() ,
            'sessions' => $sessions
        ]);
    }

    #[Route(path:"/justif-download/{justif}",name:"front_session_downloadJustif",methods:["GET","POST"])]
    public function downloadFunction(SessionStudentFiles $justif,FileManager $fileManager)  {
        return $fileManager->download($this->getParameter("files.justifs") . "/" . $justif->getId() . "/" . $justif->getFile());
    }

    #[Route(path:"/planing",name:"student_planing",methods:["GET","POST"])]
    public function planing()  {
        $sessionLines = [];
        $sessions = $this->entityManager->getRepository(SessionStudent::class)->getSessionOfStudent();
        foreach($sessions as $session){
            foreach($session->getSession()->getSession()->getSessionLines() as $sessionLine){
                if ($sessionLine->getStatus()->getId() != 2) {
                    $sessionLines[] = [
                        "title" => $sessionLine->getObjective() . " - " . $sessionLine->getTimeStart()?->format("H:i") . "|" . $sessionLine->getTimeEnd()?->format("H:i"),
                        "date" => $sessionLine->getDate()->format("Y-m-d"),
                        "allDay" => true,
                        "link" => $session->isPayed() ? $this->getParameter("meet.url") . $sessionLine->getSession()->getLink() : false,
                    ];
                }
               
            }
        }
        return new JsonResponse($sessionLines); 
    }

#[Route("/new-session", name: "student_new_session", methods: ["POST", "GET"])]
public function newSessionRequest(Request $request)
{
    $dataSession = $request->request->all("session");

     /** Session creation */
   /*  $formulas = $this->entityManager->getRepository(Formula::class)->find($dataSession["formula"]) ;
     /** @var  SessionRequest $session */
   /*  $session = new SessionRequest();
     $session->setModule($this->entityManager->getRepository(Module::class)->find($dataSession["module"]));
     $session->setType($this->entityManager->getRepository(SessionType::class)->find($dataSession["type"]));
     $session->setFormula($formulas);
     $session->setAvailability($dataSession["availability"]);
     $session->setDays($dataSession["days"]);
     $session->setHours($formulas->getTotalHours() + (int)$dataSession["additionalHours"]);
     $session->setAdditionalHours((int)$dataSession["additionalHours"]);
     $session->setTimezone($dataSession["timezone"] ?? "");
     $session->setDateStart(new \DateTime($dataSession["dateStart"]));
     $session->setStatus($this->entityManager->getRepository(SessionRequestStatus::class)->find(1));
     $session->setStudent($this->getUser());
     $this->entityManager->persist($session);

     $this->entityManager->flush();

     return $this->redirectToRoute("student_sessions_request");

}
     
    #[Route("/chat", name: "student_chat", methods: ["GET"])]
     public function chat()
     {
             return $this->render('@StudentUserBundle/chat.twig');  
    } */

#[Route("/checkout/{prestation}", name: "student_checkout", methods: ["POST", "GET"])]
public function checkout(Prestation $prestation)
{
    return $this->render('@StudentUserBundle/checkout.twig',[
        'prestation' => $prestation,
        'totalHours' => calculerHeuresCours($prestation),
        'stripePublicKey' => $this->getParameter('stripe.public.key') ,
        'prestationLines' => $prestation->getPrestationLines()->map(function($line) {
            if (!$line->isPayed()) {
                return [
                    'name' => $line->getFormula()->getName(),
                    'quantity' => $line->getQte(),
                    'amount' => intval($line->getFormula()->getPrice() * 100), // Montant en centimes
                ];
            }
        })->filter(fn($item) => $item !== null)->toArray(),
    ]);
}

#[Route("/payment-success", name: "student_payment_success", methods: ["GET"])]
#[Template("@StudentUserBundle/payement_success.twig")]
public function paymentSuccess()
{
   
}

}