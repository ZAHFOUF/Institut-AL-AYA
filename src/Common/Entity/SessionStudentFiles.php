<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Repository\SessionStudentFilesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionStudentFilesRepository::class)]
class SessionStudentFiles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sessionStudentFiles')]
    private ?SessionStudent $sessionStudent = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\Column(length: 500,nullable:true)]
    private ?string $file = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSessionStudent(): ?SessionStudent
    {
        return $this->sessionStudent;
    }

    public function setSessionStudent(?SessionStudent $sessionStudent): static
    {
        $this->sessionStudent = $sessionStudent;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): static
    {
        $this->file = $file;

        return $this;
    }
}
