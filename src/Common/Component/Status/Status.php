<?php

namespace AlAya\Common\Component\Status;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: "@componentsTemplates/Status/Status.twig",name:"Status")]
class Status
{
    public string $text = "";
    public string $color = "";

    public function mount(string $state)
    {
        if (!is_null($state)) {
            $this->text = match($state) {
            'brouillon' => 'brouillon',
            'en attendant le paiement' => 'en attente du paiement',
            'en cours' => 'en cours',
            'clôturée' => 'clôturée',
            default => 'Inconnu',
            };
            $this->color = match($state) {
            'brouillon' => 'secondary',
            'en attendant le paiement' => 'warning',
            'en cours' => 'success',
            'clôturée' => 'info',
            default => 'secondary',
            };
        }
    }
}
