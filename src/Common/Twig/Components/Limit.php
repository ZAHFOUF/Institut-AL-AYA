<?php

namespace AlAya\Common\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: "@componentsTemplates/Limit.html.twig")]
class Limit
{
    public array $range = array(5, 10, 25, 100, 250, 500);
}
