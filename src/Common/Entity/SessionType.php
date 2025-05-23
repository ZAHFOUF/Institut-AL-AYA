<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Repository\SessionTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionTypeRepository::class)]
class SessionType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Session>
     */
    #[ORM\OneToMany(targetEntity: Session::class, mappedBy: 'type')]
    private Collection $sessions;

    /**
     * @var Collection<int, FormulaSessionType>
     */
    #[ORM\OneToMany(targetEntity: FormulaSessionType::class, mappedBy: 'type')]
    private Collection $price;

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
        $this->price = new ArrayCollection();
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
     * @return Collection<int, Session>
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): static
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->setType($this);
        }

        return $this;
    }

    public function removeSession(Session $session): static
    {
        if ($this->sessions->removeElement($session)) {
            // set the owning side to null (unless already changed)
            if ($session->getType() === $this) {
                $session->setType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FormulaSessionType>
     */
    public function getPrice(): Collection
    {
        return $this->price;
    }

    public function addPrice(FormulaSessionType $price): static
    {
        if (!$this->price->contains($price)) {
            $this->price->add($price);
            $price->setType($this);
        }

        return $this;
    }

    public function removePrice(FormulaSessionType $price): static
    {
        if ($this->price->removeElement($price)) {
            // set the owning side to null (unless already changed)
            if ($price->getType() === $this) {
                $price->setType(null);
            }
        }

        return $this;
    }
}
