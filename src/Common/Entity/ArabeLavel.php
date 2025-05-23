<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Repository\ArabeLavelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArabeLavelRepository::class)]
class ArabeLavel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, StudentArabeLavel>
     */
    #[ORM\OneToMany(targetEntity: StudentArabeLavel::class, mappedBy: 'level')]
    private Collection $studentArabeLavels;

    public function __construct()
    {
        $this->studentArabeLavels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, StudentArabeLavel>
     */
    public function getStudentArabeLavels(): Collection
    {
        return $this->studentArabeLavels;
    }

    public function addStudentArabeLavel(StudentArabeLavel $studentArabeLavel): static
    {
        if (!$this->studentArabeLavels->contains($studentArabeLavel)) {
            $this->studentArabeLavels->add($studentArabeLavel);
            $studentArabeLavel->setLevel($this);
        }

        return $this;
    }

    public function removeStudentArabeLavel(StudentArabeLavel $studentArabeLavel): static
    {
        if ($this->studentArabeLavels->removeElement($studentArabeLavel)) {
            // set the owning side to null (unless already changed)
            if ($studentArabeLavel->getLevel() === $this) {
                $studentArabeLavel->setLevel(null);
            }
        }

        return $this;
    }
}
