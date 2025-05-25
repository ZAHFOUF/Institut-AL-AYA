<?php

namespace AlAya\Common\Component\Status;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: "@componentsTemplates/Status/Status.twig",name:"Status")]
class Status
{
    public string $text = "";
    public string $color = "";

    public function mount(bool $status = null, string $state = null)
    {
        if (!is_null($status)) {
            $this->text = match($status){
                true => 'Activé',
                false => 'Inactivé',
                default => 'Inactivé',
            };
            $this->color = match($status) {
                 true => 'success',
                 false => 'danger',
                 default => 'danger',
            };
        }
    }
}
