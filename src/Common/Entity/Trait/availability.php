<?php

namespace AlAya\Common\Entity\Trait;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 */
trait availability
{

    #[ORM\Column(nullable: true)]
    private ?array $availability = null;

    #[ORM\Column(nullable: true)]
    private ?array $days = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $timezone = null;

     
    public function getAvailability(): ?array
    {
        return $this->availability;
    }

    public function setAvailability(?array $availability): static
    {
        $this->availability = $availability;

        return $this;
    }

    public function getDays(): ?array
    {
        return $this->days;
    }

    public function setDays(?array $days): static
    {
        $this->days = $days;

        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(?string $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }


   
   
}
