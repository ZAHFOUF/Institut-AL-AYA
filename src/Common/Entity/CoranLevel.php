<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Repository\CoranLevelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoranLevelRepository::class)]
class CoranLevel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, StudentCoranLavel>
     */
    #[ORM\OneToMany(targetEntity: StudentCoranLavel::class, mappedBy: 'level')]
    private Collection $studentCoranLavels;

    public function __construct()
    {
        $this->studentCoranLavels = new ArrayCollection();
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
     * @return Collection<int, StudentCoranLavel>
     */
    public function getStudentCoranLavels(): Collection
    {
        return $this->studentCoranLavels;
    }

    public function addStudentCoranLavel(StudentCoranLavel $studentCoranLavel): static
    {
        if (!$this->studentCoranLavels->contains($studentCoranLavel)) {
            $this->studentCoranLavels->add($studentCoranLavel);
            $studentCoranLavel->setLevel($this);
        }

        return $this;
    }

    public function removeStudentCoranLavel(StudentCoranLavel $studentCoranLavel): static
    {
        if ($this->studentCoranLavels->removeElement($studentCoranLavel)) {
            // set the owning side to null (unless already changed)
            if ($studentCoranLavel->getLevel() === $this) {
                $studentCoranLavel->setLevel(null);
            }
        }

        return $this;
    }
}
