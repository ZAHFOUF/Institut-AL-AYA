<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Repository\WorkFlowHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkFlowHistoryRepository::class)]
class WorkFlowHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $entity = null;

    
    #[ORM\Column(type: 'string', length: 50)]
    private string $fromStep;

    #[ORM\Column(type: 'string', length: 50)]
    private string $toStep;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $transitionDate;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $performedBy;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntity(): ?string
    {
        return $this->entity;
    }

    public function setEntity(string $entity): static
    {
        $this->entity = $entity;

        return $this;
    }

    public function getFromStep(): string
    {
        return $this->fromStep;
    }

    public function setFromStep(string $fromStep): static
    {
        $this->fromStep = $fromStep;

        return $this;
    }

    public function getToStep(): string
    {
        return $this->toStep;
    }

    public function setToStep(string $toStep): static
    {
        $this->toStep = $toStep;

        return $this;
    }

    public function getTransitionDate(): \DateTimeInterface
    {
        return $this->transitionDate;
    }

    public function setTransitionDate(\DateTimeInterface $transitionDate): static
    {
        $this->transitionDate = $transitionDate;

        return $this;
    }

    public function getPerformedBy(): string
    {
        return $this->performedBy;
    }

    public function setPerformedBy(?string $performedBy): static
    {
        $this->performedBy = $performedBy;

        return $this;
    }
}
