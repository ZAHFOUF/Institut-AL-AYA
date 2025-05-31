<?php

namespace AlAya\Common\Service;

use AlAya\Common\Entity\WorkFlowHistory;
use Doctrine\Common\Lexer\Token;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Workflow\Exception\TransitionException;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Transition;

abstract class BaseWorkFlow 
{
    public  Registry $workflowRegistry;
    public EntityManagerInterface $entityManager;
    public TokenStorageInterface $security;

    public function __construct(Registry $workflowRegistry, EntityManagerInterface $entityManager, TokenStorageInterface $security,private MailerInterface $mailer)
    {
        $this->workflowRegistry = $workflowRegistry;
        $this->entityManager = $entityManager;
        $this->security = $security;
    }


    /**
     * Apply a workflow transition and call the appropriate method.
     */
    public function apply(object $entity, string $transition): void
    {
        // Get workflow dynamically
        $workflow = $this->workflowRegistry->get($entity);

        if (!$workflow->can($entity, $transition)) {
            throw new Exception("Cannot apply transition '{$transition}' on this entity.");
        }

        $fromStep = $entity->getStatus();

        // Apply transition
        $workflow->apply($entity, $transition);

        // Get new status
        $newStatus = $entity->getStatus();

        // Dynamically call method on the entity if it exists (e.g., onPending(), onApproved())
        $methodName = str_replace(' ','','on' . ucfirst($newStatus));
        if (method_exists($this, $methodName)) {
            $this->$methodName($entity);
        }

        // Save changes
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        //Save history
        $history = new WorkFlowHistory();
        $history->setEntity(get_class($entity));
        $history->setFromStep($fromStep);
        $history->setToStep($newStatus);
        $history->setPerformedBy($this->security->getToken()?->getUser()?->getUserIdentifier());
        $history->setTransitionDate(new \DateTimeImmutable());
        $this->entityManager->persist($history);
        $this->entityManager->flush();
        
    }

    

}
