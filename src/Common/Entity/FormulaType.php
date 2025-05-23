<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Repository\FormulaTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormulaTypeRepository::class)]
class FormulaType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Formula>
     */
    #[ORM\OneToMany(targetEntity: Formula::class, mappedBy: 'type')]
    private Collection $formulas;

    public function __construct()
    {
        $this->formulas = new ArrayCollection();
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
     * @return Collection<int, Formula>
     */
    public function getFormulas(): Collection
    {
        return $this->formulas;
    }

    public function addFormula(Formula $formula): static
    {
        if (!$this->formulas->contains($formula)) {
            $this->formulas->add($formula);
            $formula->setType($this);
        }

        return $this;
    }

    public function removeFormula(Formula $formula): static
    {
        if ($this->formulas->removeElement($formula)) {
            // set the owning side to null (unless already changed)
            if ($formula->getType() === $this) {
                $formula->setType(null);
            }
        }

        return $this;
    }
}
