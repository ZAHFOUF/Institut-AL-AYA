<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Repository\FormulaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormulaRepository::class)]
class Formula
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, PrestationLine>
     */
    #[ORM\OneToMany(targetEntity: PrestationLine::class, mappedBy: 'formula')]
    private Collection $prestationLines;

    #[ORM\ManyToOne(inversedBy: 'formulas')]
    private ?FormulaType $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $per = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0, nullable: true)]
    private ?string $priceProf = null;

    public function __construct()
    {
        $this->prestationLines = new ArrayCollection();
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


    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getFullName(): string
    {
        return $this->name . ' - ' . $this->price . '€/' . $this->per;
    }

    /**
     * @return Collection<int, PrestationLine>
     */
    public function getPrestationLines(): Collection
    {
        return $this->prestationLines;
    }

    public function addPrestationLine(PrestationLine $prestationLine): static
    {
        if (!$this->prestationLines->contains($prestationLine)) {
            $this->prestationLines->add($prestationLine);
            $prestationLine->setFormula($this);
        }

        return $this;
    }

    public function removePrestationLine(PrestationLine $prestationLine): static
    {
        if ($this->prestationLines->removeElement($prestationLine)) {
            // set the owning side to null (unless already changed)
            if ($prestationLine->getFormula() === $this) {
                $prestationLine->setFormula(null);
            }
        }

        return $this;
    }

    public function getType(): ?FormulaType
    {
        return $this->type;
    }

    public function setType(?FormulaType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getPer(): ?string
    {
        return $this->per;
    }

    public function setPer(?string $per): static
    {
        $this->per = $per;

        return $this;
    }

    public function getPriceProf(): ?string
    {
        return $this->priceProf;
    }

    public function setPriceProf(?string $priceProf): static
    {
        $this->priceProf = $priceProf;

        return $this;
    }

   
}
