<?php

namespace AlAya\Common\Twig\Components ;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template:"@componentsTemplates/SelectAuto.html.twig")]
class SelectAuto
{

    public string $title = ""  ;

    public string $name  = ""  ;

    public string $placeholder  = ""   ;

    public ?string $modal = null ;

    public string $api  = ""  ;

    public string $term  = "" ;

    public string $id  = "" ;
  
}