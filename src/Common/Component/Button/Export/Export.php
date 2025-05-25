<?php

namespace AlAya\Common\Component\Button\Export;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: "@componentsTemplates/Button/Export/Export.twig",name:"Button:Export")]
class Export
{

    public string $format = "Export";

    public function mount(bool $repo, string $action,array $exlude = [],string $format = "excel")
    {
        $this->format = $format;
    }
}
