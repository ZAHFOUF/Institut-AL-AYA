<?php

namespace AlAya\Common\Component\WorkFlow;

use LogicException;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: '@componentsTemplates/WorkFlow/WorkFlow.twig',name:"Workflow")]
class WorkFlow
{
    public object $entity ;
    public array $transitions = [];


    public function __construct(private Registry $workflowRegistry)
    {
        
    }

    public function mount(object $entity): void
    {
        $this->entity = $entity;
        $this->transitions = $this->workflowRegistry->get($this->entity)->getEnabledTransitions($this->entity) ;
        
    }


    public function getMetaData(Transition $transition)  {
       return $this->workflowRegistry->get($this->entity)->getMetadataStore()->getTransitionMetadata($transition);
    }

  
  /*  public function getAvailableTransitions(): array
    {
        try {
            return $this->workflow->getEnabledTransitions($this->entity);
        } catch (LogicException $e) {
            return [];
        }
    } */

}
