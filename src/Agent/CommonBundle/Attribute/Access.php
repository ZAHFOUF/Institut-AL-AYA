<?php

namespace AlAya\Agent\CommonBundle\Attribute;

#[\Attribute(\Attribute::TARGET_ALL)]
class Access
{

    private $name ;

    public function __construct(
        ?string $name = null 
    ) {
        $this->name = $name ;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
