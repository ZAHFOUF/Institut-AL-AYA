<?php

namespace AlAya\Common\Component\Button\Add;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: "@componentsTemplates/Button/Add/Add.twig",name:"Button:Add")]
class Add
{
    public string $text = "";
    public string $color = "";

    public function mount(bool $color = null, string $text = null)
    {
       
    }
}
