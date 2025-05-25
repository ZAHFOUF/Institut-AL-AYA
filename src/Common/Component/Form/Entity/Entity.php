<?php

namespace AlAya\Common\Component\Form\Entity;

use Symfony\Component\Form\FormView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: "@componentsTemplates/Form/Entity/Entity.twig",name:"Form:Entity")]
class Entity
{

    public FormView $form ;
    public string $template ;

   
}
