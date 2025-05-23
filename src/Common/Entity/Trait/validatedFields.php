<?php

namespace AlAya\Common\Entity\Trait;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 */
trait validatedFields
{

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeInterface $validatedAt = null;

    #[ORM\Column(length: 80, nullable: true)]
    private ?string $validatedBy = null;

      /**
     * Get the value of validatedAt
     */ 
    public function getValidatedAt()
    {
        return $this->validatedAt;
    }

    /**
     * Set the value of validatedAt
     *
     * @return  self
     */ 
    public function setValidatedAt($validatedAt)
    {
        $this->validatedAt = $validatedAt;

        return $this;
    }

    /**
     * Get the value of validatedBy
     */ 
    public function getValidatedBy()
    {
        return $this->validatedBy;
    }

    /**
     * Set the value of validatedBy
     *
     * @return  self
     */ 
    public function setValidatedBy($validatedBy)
    {
        $this->validatedBy = $validatedBy;

        return $this;
    }

   
   
}
