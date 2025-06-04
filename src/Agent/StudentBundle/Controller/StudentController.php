<?php

namespace AlAya\Agent\StudentBundle\Controller;

use AlAya\Agent\CommonBundle\Attribute\Access;
use AlAya\Agent\CommonBundle\Controller\Controller;
use AlAya\Common\Entity\Country;
use AlAya\Common\Entity\Language;
use AlAya\Common\Entity\Session;
use AlAya\Common\Entity\SessionType;
use AlAya\Common\Entity\Student;
use AlAya\Common\Entity\StudentGender;
use AlAya\Common\Form\StudentFormType;
use AlAya\Common\Form\StudentType;
use AlAya\Common\Repository\StudentRepository;
use AlAya\Common\Service\StudentRefresher;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;


#[Route("/student")]
#[Access]
class StudentController extends Controller
{

    public $manager ;
    public $paginator ;
    public $passwordHasher ;
    public StudentRepository $studentRepo ;

    public function __construct(EntityManagerInterface $e , PaginatorInterface $p , UserPasswordHasherInterface $ps ) {
        $this->manager = $e;
        $this->paginator = $p;
        $this->passwordHasher = $ps ;
        $this->studentRepo = $e->getRepository(Student::class);
    }


    #[Route('/', name: 'back_student_index', methods: ['GET'])]
    public function index(Request $request) : Response
    {
        $students =  $this->paginator->paginate($this->studentRepo->search($request->query),$request->query->getInt('page' , 1) , 10);
        return $this->render('@AgentStudentBundle/index.twig',[
            'students' => $students
        ]);
    }

    #[Route('/new', name: 'back_student_new', methods: ['GET','POST'])]
    public function new(Request $request,StudentRefresher $studentRefresher) : Response
    {
        $country = $this->manager->getRepository(Country::class)->findAll();
        $genders = $this->manager->getRepository(StudentGender::class)->findAll();
        $langues = $this->manager->getRepository(Language::class)->findAll();
        if ($request->isMethod('POST')) {
            $data = $request->request->all("student");
            $data["first"] = true;
            $data["acceptTerms"] = true;
            $student = $studentRefresher->refreshStudent(new Student,$data);
            $student = $studentRefresher->refreshPassword($student,$data);
            $this->manager->persist($student);
            $this->manager->flush();
            return $this->redirectToRoute("back_prestation_index",['action' => "openModal"]) ;
        }
        return $this->render("@AgentStudentBundle/new.twig",[
            'country' => $country,
            'genders' => $genders ,
            'langues' => $langues
        ]);
    }

    #[Route('/edit/{id}', name: 'back_student_edit', methods: ['GET','POST'])]
    public function edit(Request $request,StudentRefresher $studentRefresher,Student $student) : Response
    {
        if($request->isMethod("POST") && $request->request->has("student")){
            $data = $request->request->all("student");
            $data["first"] = $student->isFirst();
            $data["acceptTerms"] = $student->isAcceptTerms();
            $data["timezone"] = $student->getTimezone();
            $student = $studentRefresher->refreshStudent($student,$data);
            $this->manager->persist($student);
            $this->manager->flush();
            $this->addFlash("success","Votre profile a été mis à jour avec succès");
            return $this->redirectToRoute("back_student_index");
        }
        if($request->isMethod("POST") && $request->request->has("password_form")){
            $data = $request->request->all("password_form");
            $student = $studentRefresher->refreshPassword($student,$data);
            $this->manager->persist($student);
            $this->manager->flush();
            $this->addFlash("success","Votre mot de passe a été mis à jour avec succès");
            return $this->redirectToRoute("back_student_edit", ['id' => $student->getId()]);
        }
        $country = $this->manager->getRepository(Country::class)->findAll();
        $genders = $this->manager->getRepository(StudentGender::class)->findAll();
        $langues = $this->manager->getRepository(Language::class)->findAll();
        return $this->render('@AgentStudentBundle/edit.twig',[
            'country' => $country,
            'genders' => $genders,
            'langues' => $langues,
            'student' => $student
        ]);
    }

    


}