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
    private ?int $hoursPerWeek = null;

    #[ORM\Column]
    private ?int $totalHours = null;

    #[ORM\Column]
    private ?int $daysPerWeek = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\ManyToOne(inversedBy: 'formulas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FormulaType $type = null;

    /**
     * @var Collection<int, Session>
     */
    #[ORM\OneToMany(targetEntity: Session::class, mappedBy: 'formula')]
    private Collection $sessions;

    #[ORM\ManyToOne(inversedBy: 'formulas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Module $module = null;

    /**
     * @var Collection<int, FormulaSessionType>
     */
    #[ORM\OneToMany(targetEntity: FormulaSessionType::class, mappedBy: 'formula')]
    private Collection $formulaSessionTypes;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $avgSession = null;

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
        $this->formulaSessionTypes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHoursPerWeek(): ?int
    {
        return $this->hoursPerWeek;
    }

    public function setHoursPerWeek(int $hoursPerWeek): static
    {
        $this->hoursPerWeek = $hoursPerWeek;

        return $this;
    }

    public function getTotalHours(): ?int
    {
        return $this->totalHours;
    }

    public function setTotalHours(int $totalHours): static
    {
        $this->totalHours = $totalHours;

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

    public function getType(): ?FormulaType
    {
        return $this->type;
    }

    public function setType(?FormulaType $type): static
    {
        $this->type = $type;

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
            $session->setFormula($this);
        }

        return $this;
    }

    public function removeSession(Session $session): static
    {
        if ($this->sessions->removeElement($session)) {
            // set the owning side to null (unless already changed)
            if ($session->getFormula() === $this) {
                $session->setFormula(null);
            }
        }

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
     * @return Collection<int, FormulaSessionType>
     */
    public function getFormulaSessionTypes(): Collection
    {
        return $this->formulaSessionTypes;
    }

    public function addFormulaSessionType(FormulaSessionType $formulaSessionType): static
    {
        if (!$this->formulaSessionTypes->contains($formulaSessionType)) {
            $this->formulaSessionTypes->add($formulaSessionType);
            $formulaSessionType->setFormula($this);
        }

        return $this;
    }

    public function removeFormulaSessionType(FormulaSessionType $formulaSessionType): static
    {
        if ($this->formulaSessionTypes->removeElement($formulaSessionType)) {
            // set the owning side to null (unless already changed)
            if ($formulaSessionType->getFormula() === $this) {
                $formulaSessionType->setFormula(null);
            }
        }

        return $this;
    }

    public function getAvgSession(): ?string
    {
        return $this->avgSession;
    }

    public function setAvgSession(?string $avgSession): static
    {
        $this->avgSession = $avgSession;

        return $this;
    }

    public function getDaysPerWeek(): ?int
    {
        return $this->daysPerWeek;
    }

    public function setDaysPerWeek(int $daysPerWeek): static
    {
        $this->daysPerWeek = $daysPerWeek;

        return $this;
    }
}
