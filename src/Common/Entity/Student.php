<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Entity\Trait\availability;
use AlAya\Common\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
#[ORM\Table(name: 'student')]
class Student  implements UserInterface, PasswordAuthenticatedUserInterface
{

    use  availability ;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(nullable: true)]
    private ?int $age = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $phone = null;

    #[ORM\ManyToOne(inversedBy: 'students')]
    #[ORM\JoinColumn(nullable: false)]
    private ?StudentGender $gender = null;

    #[ORM\ManyToOne(inversedBy: 'students')]
    private ?Country $Country = null;

    /**
     * @var Collection<int, StudentLanguage>
     */
    #[ORM\OneToMany(targetEntity: StudentLanguage::class, mappedBy: 'student')]
    private Collection $studentLanguages;

    /**
     * @var Collection<int, StudentArabeLavel>
     */
    #[ORM\OneToMany(targetEntity: StudentArabeLavel::class, mappedBy: 'student')]
    private Collection $studentArabeLavels;

    /**
     * @var Collection<int, StudentCoranLavel>
     */
    #[ORM\OneToMany(targetEntity: StudentCoranLavel::class, mappedBy: 'student')]
    private Collection $studentCoranLavels;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    private ?string $timezone = null;

    /**
     * @var Collection<int, StudentSpecificNeeds>
     */
    #[ORM\OneToMany(targetEntity: StudentSpecificNeeds::class, mappedBy: 'student')]
    private Collection $studentSpecificNeeds;

    #[ORM\Column(nullable: true)]
    private ?bool $acceptTerms = null;

    #[ORM\Column(nullable: true)]
    private ?bool $acceptNotif = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $remark = null;

    /**
     * @var Collection<int, SessionLineStudent>
     */
    #[ORM\OneToMany(targetEntity: SessionLineStudent::class, mappedBy: 'student')]
    private Collection $sessionLineStudents;

    /**
     * @var Collection<int, SessionRequest>
     */
    #[ORM\OneToMany(targetEntity: SessionRequest::class, mappedBy: 'student')]
    private Collection $sessionRequests;

    /**
     * @var Collection<int, SessionStudent>
     */
    #[ORM\OneToMany(targetEntity: SessionStudent::class, mappedBy: 'student')]
    private Collection $sessionStudents;

    #[ORM\Column(nullable: true)]
    private ?bool $payed = null;

    #[ORM\Column(nullable: true)]
    private ?bool $first = null;

     /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;


    public function __construct()
    {
        $this->studentLanguages = new ArrayCollection();
        $this->studentArabeLavels = new ArrayCollection();
        $this->studentCoranLavels = new ArrayCollection();
        $this->studentSpecificNeeds = new ArrayCollection();
        $this->sessionLineStudents = new ArrayCollection();
        $this->sessionRequests = new ArrayCollection();
        $this->sessionStudents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getGender(): ?StudentGender
    {
        return $this->gender;
    }

    public function setGender(?StudentGender $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->Country;
    }

    public function setCountry(?Country $Country): static
    {
        $this->Country = $Country;

        return $this;
    }

    /**
     * @return Collection<int, StudentLanguage>
     */
    public function getStudentLanguages(): Collection
    {
        return $this->studentLanguages;
    }

    public function addStudentLanguage(StudentLanguage $studentLanguage): static
    {
        if (!$this->studentLanguages->contains($studentLanguage)) {
            $this->studentLanguages->add($studentLanguage);
            $studentLanguage->setStudent($this);
        }

        return $this;
    }

    public function removeStudentLanguage(StudentLanguage $studentLanguage): static
    {
        if ($this->studentLanguages->removeElement($studentLanguage)) {
            // set the owning side to null (unless already changed)
            if ($studentLanguage->getStudent() === $this) {
                $studentLanguage->setStudent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, StudentArabeLavel>
     */
    public function getStudentArabeLavels(): Collection
    {
        return $this->studentArabeLavels;
    }

    public function addStudentArabeLavel(StudentArabeLavel $studentArabeLavel): static
    {
        if (!$this->studentArabeLavels->contains($studentArabeLavel)) {
            $this->studentArabeLavels->add($studentArabeLavel);
            $studentArabeLavel->setStudent($this);
        }

        return $this;
    }

    public function removeStudentArabeLavel(StudentArabeLavel $studentArabeLavel): static
    {
        if ($this->studentArabeLavels->removeElement($studentArabeLavel)) {
            // set the owning side to null (unless already changed)
            if ($studentArabeLavel->getStudent() === $this) {
                $studentArabeLavel->setStudent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, StudentCoranLavel>
     */
    public function getStudentCoranLavels(): Collection
    {
        return $this->studentCoranLavels;
    }

    public function addStudentCoranLavel(StudentCoranLavel $studentCoranLavel): static
    {
        if (!$this->studentCoranLavels->contains($studentCoranLavel)) {
            $this->studentCoranLavels->add($studentCoranLavel);
            $studentCoranLavel->setStudent($this);
        }

        return $this;
    }

    public function removeStudentCoranLavel(StudentCoranLavel $studentCoranLavel): static
    {
        if ($this->studentCoranLavels->removeElement($studentCoranLavel)) {
            // set the owning side to null (unless already changed)
            if ($studentCoranLavel->getStudent() === $this) {
                $studentCoranLavel->setStudent(null);
            }
        }

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(string $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * @return Collection<int, StudentSpecificNeeds>
     */
    public function getStudentSpecificNeeds(): Collection
    {
        return $this->studentSpecificNeeds;
    }

    public function addStudentSpecificNeed(StudentSpecificNeeds $studentSpecificNeed): static
    {
        if (!$this->studentSpecificNeeds->contains($studentSpecificNeed)) {
            $this->studentSpecificNeeds->add($studentSpecificNeed);
            $studentSpecificNeed->setStudent($this);
        }

        return $this;
    }

    public function removeStudentSpecificNeed(StudentSpecificNeeds $studentSpecificNeed): static
    {
        if ($this->studentSpecificNeeds->removeElement($studentSpecificNeed)) {
            // set the owning side to null (unless already changed)
            if ($studentSpecificNeed->getStudent() === $this) {
                $studentSpecificNeed->setStudent(null);
            }
        }

        return $this;
    }

    public function isAcceptTerms(): ?bool
    {
        return $this->acceptTerms;
    }

    public function setAcceptTerms(?bool $acceptTerms): static
    {
        $this->acceptTerms = $acceptTerms;

        return $this;
    }

    public function isAcceptNotif(): ?bool
    {
        return $this->acceptNotif;
    }

    public function setAcceptNotif(?bool $acceptNotif): static
    {
        $this->acceptNotif = $acceptNotif;

        return $this;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): static
    {
        $this->remark = $remark;

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
            $sessionLineStudent->setStudent($this);
        }

        return $this;
    }

    public function removeSessionLineStudent(SessionLineStudent $sessionLineStudent): static
    {
        if ($this->sessionLineStudents->removeElement($sessionLineStudent)) {
            // set the owning side to null (unless already changed)
            if ($sessionLineStudent->getStudent() === $this) {
                $sessionLineStudent->setStudent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SessionRequest>
     */
    public function getSessionRequests(): Collection
    {
        return $this->sessionRequests;
    }

    public function addSessionRequest(SessionRequest $sessionRequest): static
    {
        if (!$this->sessionRequests->contains($sessionRequest)) {
            $this->sessionRequests->add($sessionRequest);
            $sessionRequest->setStudent($this);
        }

        return $this;
    }

    public function removeSessionRequest(SessionRequest $sessionRequest): static
    {
        if ($this->sessionRequests->removeElement($sessionRequest)) {
            // set the owning side to null (unless already changed)
            if ($sessionRequest->getStudent() === $this) {
                $sessionRequest->setStudent(null);
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

    public function addSessionStudent(SessionStudent $sessionStudent): static
    {
        if (!$this->sessionStudents->contains($sessionStudent)) {
            $this->sessionStudents->add($sessionStudent);
            $sessionStudent->setStudent($this);
        }

        return $this;
    }

    public function removeSessionStudent(SessionStudent $sessionStudent): static
    {
        if ($this->sessionStudents->removeElement($sessionStudent)) {
            // set the owning side to null (unless already changed)
            if ($sessionStudent->getStudent() === $this) {
                $sessionStudent->setStudent(null);
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

    public function isFirst(): ?bool
    {
        return $this->first;
    }

    public function setFirst(?bool $first): static
    {
        $this->first = $first;

        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLE_STUDENT'];
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

   
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

 
}
