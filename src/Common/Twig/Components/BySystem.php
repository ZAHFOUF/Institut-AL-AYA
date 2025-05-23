<?php

namespace AlAya\Common\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: "@componentsTemplates/BySystem.twig")]
class BySystem
{

    public int $icon = 0;

    public function mount( int $icon = 0)
    {
        $this->icon = boolval($icon);
    }
   
}