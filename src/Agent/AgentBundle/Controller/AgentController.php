<?php

namespace AlAya\Agent\AgentBundle\Controller;

use AlAya\Agent\AgentBundle\Form\AgentType;
use AlAya\Agent\CommonBundle\Attribute\Access;
use AlAya\Common\Entity\Agent;
use AlAya\Common\Entity\Role;
use AlAya\Common\Entity\RoleAgent;
use AlAya\Common\Entity\RolePermission;
use AlAya\Common\Service\FileManager;
use AlAya\Agent\CommonBundle\Controller\Controller;
use AlAya\Common\Entity\Group;
use AlAya\Common\Entity\Prestation;
use AlAya\Common\Entity\Session;
use AlAya\Common\Entity\SessionLine;
use AlAya\Common\Entity\Setting;
use AlAya\Common\Entity\Student;
use AlAya\Common\Repository\AgentRepository;
use AlAya\Common\Repository\SessionLineRepository;
use AlAya\Common\Repository\SessionRepository;
use AlAya\Common\Repository\SessionRequestRepository;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Psr\Container\ContainerInterface;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\Filesystem\Filesystem;

#[Route("/agent")]
#[Access]
class AgentController extends Controller
{
    public $manager ;
    public $paginator ;
    public $passwordHasher ;

    public function __construct(EntityManagerInterface $e , PaginatorInterface $p , UserPasswordHasherInterface $ps ) {
        $this->manager = $e;
        $this->paginator = $p;
        $this->passwordHasher = $ps ;
    }


    #[Route('/', name: 'back_agent_index', methods: ['GET','POST'])]
    public function index(Request $request ,AgentRepository $agentRepository)
    {
        $data = $agentRepository->search($request->query) ;

        $nbStudents = $this->manager->getRepository(Student::class)->count([]);
        $nbSession = $this->manager->getRepository(Prestation::class)->count(["status" => Prestation::ACTIVE]);

        // paginate 
       $agents = $this->paginator->paginate(
          $data,
         $request->query->getInt('page' , 1) , 
         5
     ) ;

     $formAdd = $this->createForm(AgentType::class,new Agent);

     $formAdd->handleRequest($request);


     if ($formAdd->isSubmitted() && $formAdd->isValid()) {

        /** @var Agent $agent */
         $agent = $formAdd->getData();
         $agent->setPassword($this->passwordHasher->hashPassword($agent,$agent->getPassword()));
         $agent->setDeleted(0);        
         $this->manager->persist($agent);
         $this->manager->flush();
         $this->manager->persist($agent);
         $this->manager->flush();

         $this->addFlash("success", "Utilisateur ajouté avec succès");

         return $this->redirectToRoute("back_agent_index");


     }


     return $this->render("@AgentAgentBundle/index.html.twig",[ 
        'agents' => $agents , 
        'form' => $formAdd->createView()
        ,'nbStudents' => $nbStudents,
        'nbSession' => $nbSession
        ]);   ;


    }


    #[Route('/edit/{id}', name: 'back_agent_edit', methods: ['GET','POST'])]
    public function edit(Agent $agent , Request $request,FileManager $fileManager,)
    {

     $form = $this->createForm(AgentType::class,$agent,['edit' => true]);

     $form->handleRequest($request);


     if ($form->isSubmitted() && $form->isValid()) {

         $agent = $form->getData();
         $this->manager->persist($agent);
         $this->manager->flush();

         $roles = $request->request->all("roles");

         $this->clearAgentRoles($agent->getId());

         foreach ($roles as $r) {
            $role = $this->manager->getRepository(Role::class)->find($r);
            $affect = new RoleAgent;
            $affect->setAgent($agent);
            $affect->setRole($role);
            $this->manager->persist($affect);
            $this->manager->flush();
         }

         $file = $request->files->get("sign") ;
         if (!is_null($file)) {
            $fileSystem = new Filesystem ;
            $fileSystem->remove($this->getParameter("signature.files.directory") . "/" . $agent->getId());
            $path = $fileManager->upload($file,$this->getParameter("signature.files.directory") . "/" . $agent->getId(),["jpg","png","jpeg"]);
            $agent->setSignaturePath($path);
         }

         $this->manager->persist($agent);
         $this->manager->flush();

         $this->addFlash("success", "Utilisateur modifié avec succès");

         return $this->redirectToRoute("back_agent_edit",['id' => $agent->getId(),'agent' => $agent]);


     }

     $allRoles = $this->manager->getRepository(Role::class)->findAll();
     $userRoles = $agent->getRoleAgents();


     return $this->render("@AgentAgentBundle/edit.html.twig" ,  [  'form' => $form->createView() , 'all' => $allRoles , 'usr' => $userRoles , 'agent' => $agent] );


    }

    #[Route("/params/set",name:"back_agent_set_params",methods:["GET","POST"])]
    public function setParams(Request $request)  {
       $centent = $request->getContent();
       $centent = json_decode($centent,true);
       $key = $centent["key"];
       $value = $centent["value"];
       $setting = $this->manager->getRepository(Setting::class)->findOneBy(['param' => $key]);
       $setting->setValue($value);
       $this->manager->persist($setting);
       $this->manager->flush();
       return $this->json([
           "status" => true,
           "message" => "Paramètre mis à jour avec succès"
       ]);
    }

    public function clearAgentRoles($id){
        $roles = $this->manager->getRepository(RoleAgent::class)->findBy(['agent' => $id]);
        foreach($roles as $role) {
            $this->manager->remove($role);
            $this->manager->flush();
        }
    }

}
