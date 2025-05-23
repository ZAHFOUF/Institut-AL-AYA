<?php

namespace AlAya\Common\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsTwigComponent(template: "@componentsTemplates/Status.html.twig")]
class Status
{
    public string $text = "";
    public string $color = "";
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function mount(string $status = null, string $state = null)
    {
        if (!is_null($status)) {
            $this->text = $this->translator->trans($status);
            $this->color = match($status) {
                'Acceptée' => 'success',
                'Refusée' => 'danger',
                "Liste d'attente" => 'warning',
                'Terminée' => 'success',
                'Démarrée' => 'primary',
                "Créé" => 'warning',
                "Non payé" => 'warning',
                'Payé' => 'success' ,
                default => 'warning',
            };
        }
    }
}
