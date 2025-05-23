<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Entity\Trait\availability;
use AlAya\Common\Entity\Trait\systemFields;
use AlAya\Common\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
class Session
{

    use systemFields , availability ;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SessionType $type = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SessionStatus $status = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    private ?Formula $formula = null;

    #[ORM\Column(nullable: true)]
    private ?int $hours = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    private ?Agent $teacher = null;

    #[ORM\Column(nullable: true)]
    private ?int $hoursAchivied = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateStart = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateEnd = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    private ?Module $module = null;

    #[ORM\Column(nullable: true)]
    private ?int $additionalHours = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $link = null;

    /**
     * @var Collection<int, SessionGroup>
     */
    #[ORM\OneToMany(targetEntity: SessionGroup::class, mappedBy: 'session' )]
    private Collection $sessionGroups;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $designation = null;

    /**
     * @var Collection<int, SessionLine>
     */
    #[ORM\OneToMany(targetEntity: SessionLine::class, mappedBy: 'session')]
    private Collection $sessionLines;

    /**
     * @var Collection<int, SessionStudent>
     */
    #[ORM\OneToMany(targetEntity: SessionStudent::class, mappedBy: 'session')]
    private Collection $sessionStudents;

    public function __construct()
    {
        $this->sessionGroups = new ArrayCollection();
        $this->sessionLines = new ArrayCollection();
        $this->sessionStudents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStatus(): ?SessionStatus
    {
        return $this->status;
    }

    public function setStatus(?SessionStatus $status): static
    {
        $this->status = $status;

        return $this;
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

    public function getHours(): ?int
    {
        return $this->hours;
    }

    public function setHours(?int $hours): static
    {
        $this->hours = $hours;

        return $this;
    }

    public function getTeacher(): ?Agent
    {
        return $this->teacher;
    }

    public function setTeacher(?Agent $teacher): static
    {
        $this->teacher = $teacher;

        return $this;
    }

    public function getHoursAchivied(): ?int
    {
        return $this->hoursAchivied;
    }

    public function setHoursAchivied(?int $hoursAchivied): static
    {
        $this->hoursAchivied = $hoursAchivied;

        return $this;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(?\DateTimeInterface $dateStart): static
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(?\DateTimeInterface $dateEnd): static
    {
        $this->dateEnd = $dateEnd;

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

    public function getAdditionalHours(): ?int
    {
        return $this->additionalHours;
    }

    public function setAdditionalHours(?int $additionalHours): static
    {
        $this->additionalHours = $additionalHours;

        return $this;
    }

    public function getAvailability(): ?array
    {
        return $this->availability;
    }

    public function setAvailability(?array $availability): static
    {
        $this->availability = $availability;

        return $this;
    }

    public function getDays(): ?array
    {
        return $this->days;
    }

    public function setDays(?array $days): static
    {
        $this->days = $days;

        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(?string $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }

    
    public function getSessionGroups()
    {
        return $this->sessionGroups;
    }

    public function addSessionGroup(SessionGroup $sessionStudent): static
    {
        if (!$this->sessionGroups->contains($sessionStudent)) {
            $this->sessionGroups->add($sessionStudent);
            $sessionStudent->setSession($this);
        }

        return $this;
    }

    public function removeSessionGroup(SessionGroup $sessionStudent): static
    {
        if ($this->sessionGroups->removeElement($sessionStudent)) {
            // set the owning side to null (unless already changed)
            if ($sessionStudent->getSession() === $this) {
                $sessionStudent->setGroup(null);
            }
        }

        return $this;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(?string $designation): static
    {
        $this->designation = $designation;

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
            $sessionLine->setSession($this);
        }

        return $this;
    }

    public function removeSessionLine(SessionLine $sessionLine): static
    {
        if ($this->sessionLines->removeElement($sessionLine)) {
            // set the owning side to null (unless already changed)
            if ($sessionLine->getSession() === $this) {
                $sessionLine->setSession(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SessionStudent>
     */
    public function getSessionStudents(): Collection
    {
        return $this->sessionStudents;
    }

   

    public function removeSessionStudent(SessionStudent $sessionStudent): static
    {
        if ($this->sessionStudents->removeElement($sessionStudent)) {
            // set the owning side to null (unless already changed)
            if ($sessionStudent->getSession() === $this) {
                $sessionStudent->setSession(null);
            }
        }

        return $this;
    }

    public function max(): ?\DateTimeInterface
    {
        $maxDate = null;

        foreach ($this->sessionLines as $sessionLine) {
            $lineDate = $sessionLine->getDate();
            if ($lineDate !== null && ($maxDate === null || $lineDate > $maxDate)) {
                $maxDate = $lineDate;
            }
        }

        return $maxDate;
    }

    public function min(): ?\DateTimeInterface
    {
        $minDate = null;

        foreach ($this->sessionLines as $sessionLine) {
            $lineDate = $sessionLine->getDate();
            if ($lineDate !== null && ($minDate === null || $lineDate < $minDate)) {
                $minDate = $lineDate;
            }
        }

        return $minDate;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): static
    {
        $this->link = $link;

        return $this;
    }
    
}
