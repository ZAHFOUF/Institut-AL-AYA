<?php

namespace AlAya\Common\Component\Modal;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: "@componentsTemplates/Modal/Modal.twig",name:"Modal")]
class Modal
{
    public int $count = 0;

    public function mount()
    {
       $this->count ++ ;
    }
}
