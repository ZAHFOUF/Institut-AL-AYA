<?php

namespace AlAya\Common\Component\Link\Edit;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: "@componentsTemplates/Link/Edit/Edit.twig",name:"Link:Edit")]
class Edit
{
    public string $text = "";
    public string $color = "";

    public function mount(bool $color = null, string $text = null)
    {
        
    }
}
