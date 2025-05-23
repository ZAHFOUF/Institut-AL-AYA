<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Repository\SessionLineStudentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionLineStudentRepository::class)]
class SessionLineStudent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sessionLineStudents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SessionLine $sessionLine = null;

    #[ORM\ManyToOne(inversedBy: 'sessionLineStudents')]
    private ?Student $student = null;

    #[ORM\Column(nullable: true)]
    private ?bool $present = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motif = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSessionLine(): ?SessionLine
    {
        return $this->sessionLine;
    }

    public function setSessionLine(?SessionLine $sessionLine): static
    {
        $this->sessionLine = $sessionLine;

        return $this;
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

    public function isPresent(): ?bool
    {
        return $this->present;
    }

    public function setPresent(?bool $present): static
    {
        $this->present = $present;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(?string $motif): static
    {
        $this->motif = $motif;

        return $this;
    }
}
