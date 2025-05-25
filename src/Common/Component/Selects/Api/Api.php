<?php

namespace AlAya\Common\Component\Selects\Api;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: "@componentsTemplates/Selects/Api/Api.twig",name:"Select:Api")]
class Api
{
    public ?string $repo = "";
    public ?string $function = "" ;
    public array $data = [] ;
    public ?string $name = "" ;
    public Request $request ;

    function __construct(private EntityManagerInterface $doctrine,private RequestStack $requestStack)
    {
        $this->request = $this->requestStack->getCurrentRequest();
    }
   

    public function mount(?string $repo = null,?string $function = null,?string $name = null): void
    {
          $this->name = $name ;
          $value = $this->request->get($name) ;
          $fetch = $this->doctrine->getRepository(getEntityClass($repo))->{$function}() ;
          foreach ($fetch as $row) {
             $this->data[] = [ 'id' => $row['id'] , "name" => $row['name'] ,"selected" => $row['id'] == $value ] ;
          }

    }

}
