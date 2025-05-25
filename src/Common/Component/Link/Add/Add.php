<?php

namespace AlAya\Common\Component\Link\Add;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: "@componentsTemplates/Link/Add/Add.twig",name:"Link:Add")]
class Add
{
    public string $text = "";
    public string $color = "";

    public function mount(bool $color = null, string $text = null)
    {
       
    }
}
