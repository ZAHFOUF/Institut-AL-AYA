<?php

namespace AlAya\Student\UserBundle\Controller;

use AlAya\Common\Entity\Country;
use AlAya\Common\Entity\Formula;
use AlAya\Common\Entity\Group;
use AlAya\Common\Entity\Language;
use AlAya\Common\Entity\Module;
use AlAya\Common\Entity\Prestation;
use AlAya\Common\Entity\Session;
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

    #[Route('/', name: 'student_sessions', methods: ['GET'])]
    public function sessions()
    {
        return $this->render('@StudentUserBundle/session.twig') ;
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
        $bills = $this->entityManager->getRepository(Prestation::class)->getUnPaidBills($this->getUser());
        return $this->render('@StudentUserBundle/payements.twig',[
            'bills' => $bills
        ]);
    
    }

    #[Route(path:"/planing",name:"student_planing",methods:["GET","POST"])]
    public function planing()  {
        $cours = $this->entityManager->getRepository(Session::class)->getSessionOfStudent($this->getUser());
        return new JsonResponse($cours); 
    }


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