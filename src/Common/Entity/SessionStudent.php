<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Entity\Trait\createdFields;
use AlAya\Common\Repository\SessionStudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionStudentRepository::class)]
class SessionStudent
{
    use createdFields ;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sessionStudents')]
    private ?SessionGroup $session = null;

    #[ORM\ManyToOne(inversedBy: 'sessionStudents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Student $student = null;

    #[ORM\Column(nullable: true,options:['default' => false])]
    private ?bool $payed = null;

    /**
     * @var Collection<int, SessionStudentFiles>
     */
    #[ORM\OneToMany(targetEntity: SessionStudentFiles::class, mappedBy: 'sessionStudent')]
    private Collection $sessionStudentFiles;

    #[ORM\Column(nullable: true)]
    private ?float $amount = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datePay = null;

    #[ORM\ManyToOne(inversedBy: 'sessionStudents')]
    #[ORM\JoinColumn(nullable: true,options:['default' => 1])]
    private ?PaymentType $typePay = null;

    public function __construct()
    {
        $this->sessionStudentFiles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSession(): ?SessionGroup
    {
        return $this->session;
    }

    public function setSession(?SessionGroup $session): static
    {
        $this->session = $session;

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

    public function isPayed(): ?bool
    {
        return $this->payed;
    }

    public function setPayed(?bool $payed): static
    {
        $this->payed = $payed;

        return $this;
    }

    /**
     * @return Collection<int, SessionStudentFiles>
     */
    public function getSessionStudentFiles(): Collection
    {
        return $this->sessionStudentFiles;
    }

    public function addSessionStudentFile(SessionStudentFiles $sessionStudentFile): static
    {
        if (!$this->sessionStudentFiles->contains($sessionStudentFile)) {
            $this->sessionStudentFiles->add($sessionStudentFile);
            $sessionStudentFile->setSessionStudent($this);
        }

        return $this;
    }

    public function removeSessionStudentFile(SessionStudentFiles $sessionStudentFile): static
    {
        if ($this->sessionStudentFiles->removeElement($sessionStudentFile)) {
            // set the owning side to null (unless already changed)
            if ($sessionStudentFile->getSessionStudent() === $this) {
                $sessionStudentFile->setSessionStudent(null);
            }
        }

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDatePay(): ?\DateTimeInterface
    {
        return $this->datePay;
    }

    public function setDatePay(?\DateTimeInterface $datePay): static
    {
        $this->datePay = $datePay;

        return $this;
    }

    public function getTypePay(): ?PaymentType
    {
        return $this->typePay;
    }

    public function setTypePay(?PaymentType $typePay): static
    {
        $this->typePay = $typePay;

        return $this;
    }
}
