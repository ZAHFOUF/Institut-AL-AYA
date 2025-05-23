<?php

namespace AlAya\Agent\CommonBundle\Command;

use AlAya\Common\Entity\Role;
use AlAya\Common\Entity\RolePermission;
use AlAya\Common\Entity\RolePermissions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;

#[AsCommand(
    name: 'load:permissions',
    description: 'Load permissions of the file permissions.yaml',
)]
class PermissionsCommand extends Command
{

    public $kernel ;
    public $manager ;
    public function __construct(KernelInterface $kernel , EntityManagerInterface $manager)
    {
        $this->kernel = $kernel ;
        $this->manager = $manager ;
        parent::__construct();
    }

    protected function configure(): void
    {

        $this->addOption("admin","a",InputOption::VALUE_NONE,"create the super admin");

        $this->addOption("update","u",InputOption::VALUE_NONE,"update permissions");
      
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $io = new SymfonyStyle($input, $output);

        $permissions = Yaml::parseFile( __DIR__ . "/../Config/permissions.yaml");
        $inserted = false;

        foreach ($permissions as $key => $value) {

            $check = $this->manager->getRepository(RolePermission::class)->findBy(['name' => $key]);

            if (count($check) == 0) {
                // for the insert and load
               $permission = new RolePermission;
               $permission->setName($key);
               $permission->setLabel($value['label'] ?? NULL);
               $permission->setModule($value['module'] ?? NULL);
               $permission->setDescription($value['decription'] ?? NULL);
               $this->manager->persist($permission);

               $inserted = true;

               if ($input->getOption('admin')) {

                $admin = $this->manager->getRepository(Role::class)->find(1);

                if ( is_null($admin) ) {

                    $admin = new Role;
                    $admin->setName("Super Admin");
                    $admin->setLabel("Super Admin");
    
                    $this->manager->persist($admin);
                    $this->manager->flush();

                }else{
                    $admin = $admin ;
                }

                $affect = new RolePermissions;

                $affect->setRole($admin);
                $affect->setPermission($permission);

                $this->manager->persist($affect);


                  }
            }else{

                // for the update 
              
                    if ($input->getOption('update')) {

                        $permission = $check[0];


                        $permission->setName($key);
                        $permission->setLabel($value['label'] ?? NULL);
                        $permission->setModule($value['module'] ?? NULL);
                        $permission->setDescription($value['decription'] ?? NULL);
                        $this->manager->persist($permission);
         
                        $inserted = true;

                        

                    }
            }
            
        }

        $this->manager->flush();

        $inserted ? $io->success("permissions loaded successfully") : $io->info("Nothing to load") ;

        


    
      

        return Command::SUCCESS;
    }
}
