<?php

namespace AlAya\Agent\AgentBundle\Controller;

use AlAya\Agent\AgentBundle\Form\RoleType;
use AlAya\Common\Entity\Role;
use AlAya\Common\Entity\RolePermission;
use AlAya\Common\Entity\RolePermissions;
use AlAya\Common\Repository\RolePermissionRepository;
use AlAya\Agent\CommonBundle\Attribute\Access;
use AlAya\Agent\CommonBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;


#[Route("/role")]
#[Access]
class RoleController extends Controller
{
    public $manager ;
    public $paginator ;
    public $passwordHasher ;

    public function __construct(EntityManagerInterface $e , PaginatorInterface $p , UserPasswordHasherInterface $ps ) {
        $this->manager = $e;
        $this->paginator = $p;
        $this->passwordHasher = $ps ;
    }


    #[Route('/', name: 'back_role_index', methods: ['GET','POST'])]
    public function index(Request $request,RolePermissionRepository $repo)
    {

        $form = $this->createForm(RoleType::class,new Role);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $role = $form->getData();
            $this->manager->persist($role);
            $this->manager->flush();

            $permissions = $request->request->all("permissions");

            $this->clearRolePermissions($role->getId());

           

            foreach ($permissions as $p) {
                $permission = $this->manager->getRepository(RolePermission::class)->find($p);
                $affect = new RolePermissions;
                $affect->setRole($role);
                $affect->setPermission($permission);
                $this->manager->persist($affect);
                $this->manager->flush();
            }

           
            $this->addFlash("success" , "Rôle ajouter avec succès");
            return $this->redirectToRoute("back_role_index");
        }

       
       
        $allPermission = $this->manager->getRepository(RolePermission::class)->findAll();
        $roles =   $this->manager->getRepository(Role::class)->findAll();

   



     return $this->render("@AgentAgentBundle/role.html.twig", [ 'roles' => $roles , 'form' => $form->createView() , "repo" => $repo ] ) ;


    }


    #[Route('/edit/{id}', name: 'back_role_edit', methods: ['GET','POST'])]
    public function edit(Role $role , Request $request,RolePermissionRepository $repo)
    {
         

        $form = $this->createForm(RoleType::class,$role);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $role = $form->getData();
            $this->manager->persist($role);
            $this->manager->flush();

            $permissions = $request->request->all("permissions");


            $this->clearRolePermissions($role->getId());

            foreach ($permissions as $p) {
                $permission = $this->manager->getRepository(RolePermission::class)->find($p);
                $affect = new RolePermissions;
                $affect->setRole($role);
                $affect->setPermission($permission);
                $this->manager->persist($affect);
                $this->manager->flush();
            }

           $this->manager->flush();


           
            $this->addFlash("success" , "Rôle modifié avec succès");
            return $this->redirectToRoute("back_role_edit",['id' => $role->getId()]);
        }

        $userPermission = array_values(array_column( $this->manager->getRepository(RolePermissions::class)->findByRole($role->getId()),"id")) ;
      
        return $this->render("@AgentAgentBundle/role.edit.html.twig" , [ 'form' => $form->createView() , "repo" => $repo , 'usp' => $userPermission ]) ;


    }


  
    public function clearRolePermissions($id){
        $permissions = $this->manager->getRepository(RolePermissions::class)->findBy(['role' => $id]);
        foreach($permissions as $permission) {
            $this->manager->remove($permission);
            $this->manager->flush();
        }
    }

    
  

}
