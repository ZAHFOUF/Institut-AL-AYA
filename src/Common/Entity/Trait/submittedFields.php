<?php

namespace AlAya\Common\Entity\Trait;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 */
trait submittedFields
{

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeInterface $submittedAt = null;


      /**
     * Get the value of SubmittedAt
     */ 
    public function getSubmittedAt()
    {
        return $this->submittedAt;
    }

    /**
     * Set the value of submittedAt
     *
     * @return  self
     */ 
    public function setSubmittedAt($submittedAt)
    {
        $this->submittedAt = $submittedAt;

        return $this;
    }

  
   
}
