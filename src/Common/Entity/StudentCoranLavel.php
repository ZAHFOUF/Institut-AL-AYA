<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Repository\StudentCoranLavelRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentCoranLavelRepository::class)]
class StudentCoranLavel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'studentCoranLavels')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Student $student = null;

    #[ORM\ManyToOne(inversedBy: 'studentCoranLavels')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CoranLevel $level = null;

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

    public function getLevel(): ?CoranLevel
    {
        return $this->level;
    }

    public function setLevel(?CoranLevel $level): static
    {
        $this->level = $level;

        return $this;
    }
}
