<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Repository\StudentSpecificNeedsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentSpecificNeedsRepository::class)]
class StudentSpecificNeeds
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'studentSpecificNeeds')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Student $student = null;

    #[ORM\ManyToOne(inversedBy: 'studentSpecificNeeds')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SpecificNeeds $specificNeed = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): static
    {
        $this->student = $student;

        return $this;
    }

    public function getSpecificNeed(): ?SpecificNeeds
    {
        return $this->specificNeed;
    }

    public function setSpecificNeed(?SpecificNeeds $specificNeed): static
    {
        $this->specificNeed = $specificNeed;

        return $this;
    }
}
