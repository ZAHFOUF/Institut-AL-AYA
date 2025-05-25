<?php

namespace AlAya\Common\Component\Table;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: '@componentsTemplates/Table/Table.twig',name:"Table")]
class Table
{
    public array $head = []; // Column headers
}
