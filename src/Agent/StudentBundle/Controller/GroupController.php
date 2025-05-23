<?php

namespace AlAya\Agent\StudentBundle\Controller;

use AlAya\Agent\CommonBundle\Attribute\Access;
use AlAya\Agent\CommonBundle\Controller\Controller;
use AlAya\Agent\CommonBundle\Service\SessionRefresher;
use AlAya\Common\Entity\Session;
use AlAya\Common\Entity\Group;
use AlAya\Common\Entity\SessionGroup;
use AlAya\Common\Entity\SessionRequest;
use AlAya\Common\Entity\Student;
use AlAya\Common\Form\GroupForm;
use AlAya\Common\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;


#[Route("/group")]
#[Access]
class GroupController extends Controller
{

    public $manager ;
    public $paginator ;
    public $passwordHasher ;
    public GroupRepository $groupRepo ;

    public function __construct(EntityManagerInterface $e , PaginatorInterface $p , UserPasswordHasherInterface $ps ) {
        $this->manager = $e;
        $this->paginator = $p;
        $this->passwordHasher = $ps ;
        $this->groupRepo = $e->getRepository(Group::class);
    }


    #[Route('/', name: 'back_group_index', methods: ['GET','POST'])]
    public function index(Request $request) : Response
    {
        
        $form = $this->createForm(GroupForm::class,new Group);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $group = $form->getData();
            $this->manager->persist($group);
            $this->manager->flush();
            $this->addFlash("success" , "Classe ajouter avec succès");
            return $this->redirectToRoute("back_group_index");
        }



        $groups =  $this->paginator->paginate($this->groupRepo->all($request->query),$request->query->getInt('page' , 1) , 10);
        return $this->render('@AgentStudentBundle/Groups/index.twig',[
            'groups' => $groups ,
            'form' => $form->createView()
        ]);
    }


    #[Route('/show/{group}', name: 'back_group_show', methods: ['GET','POST'])]
    public function show(Group $group,Request $request,PaginatorInterface $paginatorInterface,SessionRefresher $sessionRefresher) : Response
    {

        $repo = $this->manager->getRepository(Student::class) ;
        

        $activeStudents = $repo->findBy(['id' => $group->getStudents() ]) ;
        $canceledStudents = $repo->findBy([ 'id' => $group->getCancelStudents()  ]);


        
        

        if ($request->isMethod("POST") and $request->request->has("students")) {
            $list = $request->request->all("students") ;
            $group->setStudents(array_merge($group->getStudents(),$list));
            $this->manager->persist($group);
            $this->manager->flush();
            $sessionRefresher->autoRefresh($group,$list);
            $this->addFlash("success","Ajouté avec succès");
            return $this->redirectToRoute("back_group_show",['group' => $group->getId()]);
        }



        $form = $this->createForm(GroupForm::class,$group,["edit" => true]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $group = $form->getData();
            $this->manager->persist($group);
            $this->manager->flush();
            $this->addFlash("success" , "Classe éditer avec succès");
            return $this->redirectToRoute("back_group_show",[ 'group' => $group->getId() ]);
        }
      

       
        $sessions = $paginatorInterface->paginate($this->manager->getRepository(SessionGroup::class)->findBy(['group' => $group]),$request->query->get("page",1),10) ;

        $lien = false ;

        if (!is_null($group->getInvitationUuid()) and !empty($group->getInvitationUuid())) {
           $lien =  $this->getParameter("project.host") . $this->generateUrl("tech_student_invitation",['inv' => $group->getInvitationUuid()]);
        }


        return $this->render('@AgentStudentBundle/Groups/show.twig',[
            'group' => $group ,
            'form' => $form->createView() ,
            'activeStudents' => $activeStudents ,
            'canceledStudents' => $canceledStudents ,
            'students' => $repo->studentsGroup($group->getGender()),
            "sessions" => $sessions,
             "lien" => $lien
        ]);
    }

    #[Route('/abandon/{group}', name: 'back_group_abandon', methods: ['POST'])]
    public function abandon(Group $group,Request $request) : Response
    {
        $list = $request->request->all("choises") ;
        // Update the active students in the group
        $group->setStudents(array_filter($group->getStudents(),function($student) use ($list){
            return !in_array($student,$list);
        }));
        $group->setCancelStudents(array_merge($group->getCancelStudents() ?? [],$list));
        $this->manager->persist($group);
        $this->manager->flush();
        $this->addFlash("success","Abandonné avec succès");
        return $this->redirectToRoute("back_group_show",['group' => $group->getId()]);
    }

}