<?php

namespace AlAya\Common\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security as SecurityBundleSecurity;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class EntityService
{
    private $manager;
    private $requestStack;
    private $security;

    public function __construct(EntityManagerInterface $manager, RequestStack $requestStack, SecurityBundleSecurity $security)
    {
        $this->manager = $manager;
        $this->requestStack = $requestStack;
        $this->security = $security;
    }

    public function insert($object, $isCreate = true) : void
    {
       
        $username = '';
        if ($this->security->getUser() && !is_string($this->security->getUser())) {
            $username = $this->security->getUser()->getUserIdentifier();
        }

        if (method_exists($object, 'setCreatedBy') && $isCreate) {
            $object->setCreatedBy($username);
        }

        if (method_exists($object, "setCreatedAt") && $isCreate) {
            $object->setCreatedAt(new \DateTimeImmutable());
        }

        if (method_exists($object, 'setCreatedIp')) {
            $object->setCreatedIp($this->requestStack->getCurrentRequest()->getClientIp());
        } 

        if (method_exists($object, 'setUpdatedBy')) {
            $object->setUpdatedBy($username);
        }

        if (method_exists($object, 'setUpdatedAt')) {
            $object->setUpdatedAt(new \DateTimeImmutable());
        }

        if (method_exists($object, 'setEnabled')) {
            $object->setEnabled(1);
        }
        
        if (method_exists($object, 'setDeleted')) {
            $object->setDeleted(0);
        }
        
        $this->manager->persist($object);
        $this->manager->flush();
    }

    public function update($object)
    {
        $username = '';
        if ($this->security->getUser()) {
            $username = $this->security->getUser()->getUserIdentifier();
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
