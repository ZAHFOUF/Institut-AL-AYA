<?php

namespace AlAya\Common\Entity\Trait;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 */
trait systemFields
{

    #[ORM\Column(nullable: true)]
    private ?bool $bySystem = null;


    public function isBySystem(): ?bool
    {
        return $this->bySystem;
    }

    public function setBySystem(?bool $bySystem): static
    {
        $this->bySystem = $bySystem;

        return $this;
    }

}