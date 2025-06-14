<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Repository\BillRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BillRepository::class)]
class Bill
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bills')]
    private ?Prestation $prestation = null;

    #[ORM\Column]
    private ?int $hours = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $amount = null;

    /**
     * @var Collection<int, PrestationLine>
     */
    #[ORM\OneToMany(targetEntity: PrestationLine::class, mappedBy: 'bill')]
    private Collection $prestationLines;

    #[ORM\Column(nullable: true)]
    private ?bool $payed = null;

    #[ORM\Column(nullable: true)]
    private ?int $rateByHour = null;

    /**
     * @var Collection<int, Payement>
     */
    #[ORM\OneToMany(targetEntity: Payement::class, mappedBy: 'bill')]
    private Collection $payements;

    public function __construct()
    {
        $this->prestationLines = new ArrayCollection();
        $this->payements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrestation(): ?Prestation
    {
        return $this->prestation;
    }

    public function setPrestation(?Prestation $prestation): static
    {
        $this->prestation = $prestation;

        return $this;
    }

    public function getHours(): ?int
    {
        return $this->hours;
    }

    public function setHours(int $hours): static
    {
        $this->hours = $hours;

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

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;

        return $this;
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
            $prestationLine->setBill($this);
        }

        return $this;
    }

    public function removePrestationLine(PrestationLine $prestationLine): static
    {
        if ($this->prestationLines->removeElement($prestationLine)) {
            // set the owning side to null (unless already changed)
            if ($prestationLine->getBill() === $this) {
                $prestationLine->setBill(null);
            }
        }

        return $this;
    }

    public function isPayed(): ?bool
    {
        return $this->payed;
    }

    public function setPayed(?bool $payed): static
    {
        $this->payed = $payed;

        return $this;
    }

    public function getRateByHour(): ?int
    {
        return $this->rateByHour;
    }

    public function setRateByHour(?int $rateByHour): static
    {
        $this->rateByHour = $rateByHour;

        return $this;
    }

    /**
     * @return Collection<int, Payement>
     */
    public function getPayements(): Collection
    {
        return $this->payements;
    }

    public function addPayement(Payement $payement): static
    {
        if (!$this->payements->contains($payement)) {
            $this->payements->add($payement);
            $payement->setBill($this);
        }

        return $this;
    }

    public function removePayement(Payement $payement): static
    {
        if ($this->payements->removeElement($payement)) {
            // set the owning side to null (unless already changed)
            if ($payement->getBill() === $this) {
                $payement->setBill(null);
            }
        }

        return $this;
    }
}
