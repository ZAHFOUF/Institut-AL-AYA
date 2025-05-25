<?php

namespace AlAya\Common\Component\Selects\Normal;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: "@componentsTemplates/Selects/Normal/Normal.twig",name:"Select")]
class Normal
{


    public array $data = [] ;
    public ?string $label ;
    public ?string $name  ;
    public Request $request ;

    function __construct(private RequestStack $requestStack)
    {
        $this->request = $this->requestStack->getCurrentRequest();
    }


    public function mount(array $data,?string $name = null)
    {
        $this->name = $name ;
        $val = $this->request->get($name) ;
        foreach ($data as $row => $value) {
           $this->data[] = [ 'id' =>  $value , "name" =>  $row ,"selected" => $value == $val ];
        }
    }

}
