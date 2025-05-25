<?php

namespace AlAya\Common\Component\ReportingTable;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: '@componentsTemplates/ReportingTable/ReportingTable.twig',name:"ReportingTable")]
class ReportingTable
{
    public array $data = []; // data

    public function mount()
    {

    }
}
