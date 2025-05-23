<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Entity\Trait\systemFields;
use AlAya\Common\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
class Group
{

    use systemFields ;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $max = null;

    #[ORM\ManyToOne(inversedBy: 'groups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?StudentGender $gender = null;

    #[ORM\Column(nullable:true)]
    private ?array $students = null;

    #[ORM\Column(nullable:true)]
    private ?array $cancelStudents = null;

    /**
     * @var Collection<int, SessionRequest>
     */
    #[ORM\OneToMany(targetEntity: SessionRequest::class, mappedBy: 'classe')]
    private Collection $sessionRequests;

    /**
     * @var Collection<int, SessionGroup>
     */
    #[ORM\OneToMany(targetEntity: SessionGroup::class, mappedBy: 'group')]
    private Collection $sessions;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $invitationUuid = null;

    #[ORM\ManyToOne(inversedBy: 'myGroups')]
    private ?Agent $teacher = null;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'groupe')]
    private Collection $messages;


    public function __construct()
    {
        $this->sessionRequests = new ArrayCollection();
        $this->sessions = new ArrayCollection ;
        $this->messages = new ArrayCollection();
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

    public function getMax(): ?int
    {
        return $this->max;
    }

    public function setMax(int $max): static
    {
        $this->max = $max;

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

    public function getStudents(): ?array
    {
        return $this->students;
    }

    public function setCancelStudents(?array $students): static
    {
        $this->cancelStudents = $students;

        return $this;
    }

    public function getCancelStudents(): ?array
    {
        return $this->cancelStudents;
    }

    public function setStudents(?array $students): static
    {
        $this->students = $students;

        return $this;
    }


    /**
     * @return Collection<int, SessionRequest>
     */
    public function getSessionRequests(): Collection
    {
        return $this->sessionRequests;
    }


    /**
     * @return Collection<int, SessionRequest>
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSessionRequest(SessionRequest $sessionRequest): static
    {
        if (!$this->sessionRequests->contains($sessionRequest)) {
            $this->sessionRequests->add($sessionRequest);
            $sessionRequest->setClasse($this);
        }

        return $this;
    }

    public function removeSessionRequest(SessionRequest $sessionRequest): static
    {
        if ($this->sessionRequests->removeElement($sessionRequest)) {
            // set the owning side to null (unless already changed)
            if ($sessionRequest->getClasse() === $this) {
                $sessionRequest->setClasse(null);
            }
        }

        return $this;
    }

    public function studentsCount()
    {
        return count($this->students ?? []);
    }

    public function sessionCount()
    {
        return $this->getSessions()->count();
    }

    public function isBySystem(): ?bool
    {
        return $this->bySystem;
    }

    public function setBySystem(?bool $bySystem): static
    {
        $this->bySystem = $bySystem;

        return $this;
    }

    public function getInvitationUuid(): ?string
    {
        return $this->invitationUuid;
    }

    public function setInvitationUuid(?string $invitationUuid): static
    {
        $this->invitationUuid = $invitationUuid;

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

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setGroupe($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getGroupe() === $this) {
                $message->setGroupe(null);
            }
        }

        return $this;
    }


}
