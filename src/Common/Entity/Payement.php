<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Repository\PayementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PayementRepository::class)]
class Payement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?float $amount = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(targetEntity: PayementType::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?PayementType $type = null;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $stripeId = null;

    #[ORM\ManyToOne(inversedBy: 'payements')]
    private ?Bill $bill = null;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getType(): ?PayementType
    {
        return $this->type;
    }

    public function setType(?PayementType $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getStripeId(): ?string
    {
        return $this->stripeId;
    }

    public function setStripeId(string $stripeId): static
    {
        $this->stripeId = $stripeId;

        return $this;
    }

    public function getBill(): ?Bill
    {
        return $this->bill;
    }

    public function setBill(?Bill $bill): static
    {
        $this->bill = $bill;

        return $this;
    }
}
