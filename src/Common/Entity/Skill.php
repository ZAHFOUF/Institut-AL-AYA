<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Repository\SkillRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SkillRepository::class)]
class Skill
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'skills')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Module $module = null;

    /**
     * @var Collection<int, SessionLine>
     */
    #[ORM\OneToMany(targetEntity: SessionLine::class, mappedBy: 'skill')]
    private Collection $sessionLines;

    public function __construct()
    {
        $this->sessionLines = new ArrayCollection();
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

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function setModule(?Module $module): static
    {
        $this->module = $module;

        return $this;
    }

    /**
     * @return Collection<int, SessionLine>
     */
    public function getSessionLines(): Collection
    {
        return $this->sessionLines;
    }

    public function addSessionLine(SessionLine $sessionLine): static
    {
        if (!$this->sessionLines->contains($sessionLine)) {
            $this->sessionLines->add($sessionLine);
            $sessionLine->setSkill($this);
        }

        return $this;
    }

    public function removeSessionLine(SessionLine $sessionLine): static
    {
        if ($this->sessionLines->removeElement($sessionLine)) {
            // set the owning side to null (unless already changed)
            if ($sessionLine->getSkill() === $this) {
                $sessionLine->setSkill(null);
            }
        }

        return $this;
    }
}
