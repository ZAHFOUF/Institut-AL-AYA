<?php 

namespace AlAya\Agent\CommonBundle\Controller ;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class Controller extends AbstractController 
{

    public $manager ;
  


    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager ;
    }

   
    /**
     * Set to the object default fieled (CreatedAt ...) when the object created
     *
     * @param class $object
     * @param boolean $isCreate
     * @return void
     */
    public function insert( $object , $isCreate = true) : void
    {
        $username = '';
        if ($this->getUser() && !is_string($this->getUser())) {
            $username = $this->getUser()->getUsername();
        }

        if (method_exists($object, 'setCreatedBy') && $isCreate) {
            $object->setCreatedBy($username);
        }

        if (method_exists($object, "setCreatedAt")) {
            if ($isCreate) {
                $object->setCreatedAt(new \DateTime());
            }
        }

        if (method_exists($object, 'setCreatedIp')) {
            $object->setCreatedIp($this->container->get('request_stack')->getCurrentRequest()->getClientIp());
        } 

        if (method_exists($object, 'setUpdatedBy')) {
            $object->setUpdatedBy($username);
        }

        if (method_exists($object, 'setUpdatedAt')) {
            $object->setUpdatedAt(new \DateTime());
        }
            $this->manager->persist($object);
            $this->manager->flush($object);

        }

       /**
        * Set to the object default fieled (UpdatedAt ...) when the object updated
        *
        * @param class $object
        * @param boolean $isCreate
        * @return void
        */
        public function update($object)
        {
            $username = '';
            if ($this->getUser()) {
                $username = $this->getUser()->getUsername();
            }
    
            if (method_exists($object, 'setUpdatedBy')) {
                $object->setUpdatedBy($username);
            }
    
            if (method_exists($object, 'setUpdatedAt')) {
                $object->setUpdatedAt(new \DateTime());
            }
    
            $this->manager->persist($object);
            $this->manager->flush();
        }
        

      
     
    }


    
