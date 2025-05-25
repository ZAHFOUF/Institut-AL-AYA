<?php

namespace AlAya\Common\Component\Pdf;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: "@componentsTemplates/Pdf/Pdf.twig",name:"Pdf")]
class Pdf
{
   
    public function mount()
    {
       
    }
}
