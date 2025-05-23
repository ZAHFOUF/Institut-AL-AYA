<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Entity\Trait\createdFields;
use AlAya\Common\Entity\Trait\submittedFields;
use AlAya\Common\Entity\Trait\updatedFields;
use AlAya\Common\Entity\Trait\validatedFields;
use AlAya\Common\Repository\SessionRequestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionRequestRepository::class)]
class SessionRequest
{

    use submittedFields , validatedFields , createdFields , updatedFields ;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SessionType $type = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    private ?Formula $formula = null;

    #[ORM\Column(nullable: true)]
    private ?int $hours = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateStart = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateEnd = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    private ?Module $module = null;

    #[ORM\Column(nullable: true)]
    private ?int $additionalHours = null;

    #[ORM\Column(nullable: true)]
    private ?array $availability = null;

    #[ORM\Column(nullable: true)]
    private ?array $days = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $timezone = null;

    #[ORM\ManyToOne(inversedBy: 'sessionRequests')]
    private ?SessionRequestStatus $status = null;

    #[ORM\ManyToOne(inversedBy: 'sessionRequests')]
    private ?Student $student = null;

    #[ORM\ManyToOne(inversedBy: 'sessionRequests')]
    private ?Group $classe = null;

    #[ORM\Column(length: 255,nullable:true)]
    private ?string $motif = null;

    #[ORM\ManyToOne(inversedBy: 'sessionRequests')]
    private ?GroupRequestType $groupType = null;

    #[ORM\Column(nullable: true)]
    private ?int $maxStudent = null;

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

    public function getStatus(): ?SessionRequestStatus
    {
        return $this->status;
    }

    public function setStatus(?SessionRequestStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): static
    {
        $this->student = $student;

        return $this;
    }

    public function getClasse(): ?Group
    {
        return $this->classe;
    }

    public function setClasse(?Group $classe): static
    {
        $this->classe = $classe;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(?string $motif): static
    {
        $this->motif = $motif;

        return $this;
    }

    public function getGroupType(): ?GroupRequestType
    {
        return $this->groupType;
    }

    public function setGroupType(?GroupRequestType $groupType): static
    {
        $this->groupType = $groupType;

        return $this;
    }

    public function getMaxStudent(): ?int
    {
        return $this->maxStudent;
    }

    public function setMaxStudent(?int $maxStudent): static
    {
        $this->maxStudent = $maxStudent;

        return $this;
    }

}
