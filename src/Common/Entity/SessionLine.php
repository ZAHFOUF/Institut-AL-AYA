<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Entity\Trait\deletedFields;
use AlAya\Common\Repository\SessionLineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionLineRepository::class)]
class SessionLine
{

    use deletedFields ;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sessionLines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Session $session = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $timeStart = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $timeEnd = null;

    #[ORM\Column(nullable: true)]
    private ?float $hours = null;

    #[ORM\Column(length: 500,nullable:true)]
    private ?string $objective = null;

    #[ORM\ManyToOne(inversedBy: 'sessionLines')]
    private ?Skill $skill = null;

    #[ORM\ManyToOne(inversedBy: 'sessionLines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SessionLineStatus $status = null;

    /**
     * @var Collection<int, SessionLineStudent>
     */
    #[ORM\OneToMany(targetEntity: SessionLineStudent::class, mappedBy: 'sessionLine')]
    private Collection $sessionLineStudents;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $link = null;

    public function __construct()
    {
        $this->sessionLineStudents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): static
    {
        $this->session = $session;

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

    public function getTimeStart(): ?\DateTimeInterface
    {
        return $this->timeStart;
    }

    public function setTimeStart(?\DateTimeInterface $timeStart): static
    {
        $this->timeStart = $timeStart;

        return $this;
    }

    public function getTimeEnd(): ?\DateTimeInterface
    {
        return $this->timeEnd;
    }

    public function setTimeEnd(?\DateTimeInterface $timeEnd): static
    {
        $this->timeEnd = $timeEnd;

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

    public function getObjective(): ?string
    {
        return $this->objective;
    }

    public function setObjective(string $objective): static
    {
        $this->objective = $objective;

        return $this;
    }

    public function getSkill(): ?Skill
    {
        return $this->skill;
    }

    public function setSkill(?Skill $skill): static
    {
        $this->skill = $skill;

        return $this;
    }

    public function getStatus(): ?SessionLineStatus
    {
        return $this->status;
    }

    public function setStatus(?SessionLineStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, SessionLineStudent>
     */
    public function getSessionLineStudents(): Collection
    {
        return $this->sessionLineStudents;
    }

    public function addSessionLineStudent(SessionLineStudent $sessionLineStudent): static
    {
        if (!$this->sessionLineStudents->contains($sessionLineStudent)) {
            $this->sessionLineStudents->add($sessionLineStudent);
            $sessionLineStudent->setSessionLine($this);
        }

        return $this;
    }

    public function removeSessionLineStudent(SessionLineStudent $sessionLineStudent): static
    {
        if ($this->sessionLineStudents->removeElement($sessionLineStudent)) {
            // set the owning side to null (unless already changed)
            if ($sessionLineStudent->getSessionLine() === $this) {
                $sessionLineStudent->setSessionLine(null);
            }
        }

        return $this;
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
