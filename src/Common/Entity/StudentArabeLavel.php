<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Repository\StudentArabeLavelRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentArabeLavelRepository::class)]
class StudentArabeLavel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'studentArabeLavels')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Student $student = null;

    #[ORM\ManyToOne(inversedBy: 'studentArabeLavels')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ArabeLavel $level = null;

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

    public function getLevel(): ?ArabeLavel
    {
        return $this->level;
    }

    public function setLevel(?ArabeLavel $level): static
    {
        $this->level = $level;

        return $this;
    }
}
