<?php

namespace AlAya\Common\Component\Link\Delete;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: "@componentsTemplates/Link/Delete/Delete.twig",name:"Link:Delete")]
class Delete
{
    public ?string $entity = "";
    public ?string $entityId = "" ;

    public function mount(?string $entity = null,?string $entityId = null)
    {
        $this->entity = $entity ;
        $this->entityId = $entityId ;
    }

}
