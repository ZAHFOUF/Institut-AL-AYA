<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Repository\FormulaSessionTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormulaSessionTypeRepository::class)]
class FormulaSessionType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'formulaSessionTypes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Formula $formula = null;

    #[ORM\ManyToOne(inversedBy: 'price')]
    private ?SessionType $type = null;

    #[ORM\Column]
    private ?int $price = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFormula(): ?Formula
    {
        return $this->formula;
    }

    public function setFormula(?Formula $formula): static
    {
        $this->formula = $formula;

        return $this;
    }

    public function getType(): ?SessionType
    {
        return $this->type;
    }

    public function setType(?SessionType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }
}
