<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Repository\SessionLineStatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionLineStatusRepository::class)]
class SessionLineStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, SessionLine>
     */
    #[ORM\OneToMany(targetEntity: SessionLine::class, mappedBy: 'status')]
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
            $sessionLine->setStatus($this);
        }

        return $this;
    }

    public function removeSessionLine(SessionLine $sessionLine): static
    {
        if ($this->sessionLines->removeElement($sessionLine)) {
            // set the owning side to null (unless already changed)
            if ($sessionLine->getStatus() === $this) {
                $sessionLine->setStatus(null);
            }
        }

        return $this;
    }
}
