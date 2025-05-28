<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Entity\RoleAgent;
use AlAya\Common\Entity\Trait\availability;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use AlAya\Common\Entity\Trait\createdFields;
use AlAya\Common\Entity\Trait\deletedFields;
use AlAya\Common\Entity\Trait\updatedFields;
use AlAya\Common\Repository\AgentRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: AgentRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Il existe déjà un compte avec cet email')]
#[UniqueEntity(fields: ['username'], message: "Il existe déjà un compte avec ce nom d'utilisateur")]
class Agent implements UserInterface, PasswordAuthenticatedUserInterface
{

    use createdFields , updatedFields , deletedFields , availability ;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(nullable: true)]
    private ?bool $enabled = null;

    #[ORM\OneToMany(mappedBy: 'agent', targetEntity: RoleAgent::class)]
    private $roleAgents;

    #[ORM\Column(length: 100, nullable: false, unique: true)]
    private ?string $username = null;

    #[ORM\ManyToOne(inversedBy: 'agents',targetEntity: AgentType::class)]
    #[ORM\JoinColumn(nullable: false)]
    private AgentType $type ;

    /**
     * @var Collection<int, Session>
     */
    #[ORM\OneToMany(targetEntity: Session::class, mappedBy: 'teacher')]
    private Collection $sessions;

    #[ORM\Column(nullable: true)]
    private ?bool $dispo = null;

    #[ORM\Column(nullable: true)]
    private ?float $price = null;

    #[ORM\Column(nullable: true)]
    private ?int $maxHours = null;

    /**
     * @var Collection<int, Group>
     */
    #[ORM\OneToMany(targetEntity: Group::class, mappedBy: 'teacher')]
    private Collection $myGroups;

    #[ORM\ManyToOne(inversedBy: 'agents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?StudentGender $gender = null;

    #[ORM\ManyToOne(inversedBy: 'agents')]
    private ?Module $module = null;

    /**
     * @var Collection<int, Prestation>
     */
    #[ORM\OneToMany(targetEntity: Prestation::class, mappedBy: 'agent')]
    private Collection $prestations;

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
        $this->myGroups = new ArrayCollection();
        $this->prestations = new ArrayCollection();
    }

   

 

    public function __toString() {
        return $this->lastName.' '.$this->firstName;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
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
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
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

    /**
     * @see UserInterface
     */
    public function eraseCredentials() :void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFullName()  {
        return $this->firstName.' '.$this->lastName;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(?bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return Collection<int, RoleAgent>
     */
    public function getRoleAgents(): Collection
    {
        return $this->roleAgents;
    }

    public function addRoleAgent(RoleAgent $roleAgent): self
    {
        if (!$this->roleAgents->contains($roleAgent)) {
            $this->roleAgents[] = $roleAgent;
            $roleAgent->setAgent($this);
        }

        return $this;
    }

    public function removeRoleAgent(RoleAgent $roleAgent): self
    {
        if ($this->roleAgents->removeElement($roleAgent)) {
            // set the owning side to null (unless already changed)
            if ($roleAgent->getAgent() === $this) {
                $roleAgent->setAgent(null);
            }
        }

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getType(): ?AgentType
    {
        return $this->type;
    }

    public function setType(?AgentType $type): static
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
            $session->setTeacher($this);
        }

        return $this;
    }

    public function removeSession(Session $session): static
    {
        if ($this->sessions->removeElement($session)) {
            // set the owning side to null (unless already changed)
            if ($session->getTeacher() === $this) {
                $session->setTeacher(null);
            }
        }

        return $this;
    }

    public function isDispo(): ?bool
    {
        return $this->dispo;
    }

    public function setDispo(?bool $dispo): static
    {
        $this->dispo = $dispo;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getMaxHours(): ?int
    {
        return $this->maxHours;
    }

    public function setMaxHours(?int $maxHours): static
    {
        $this->maxHours = $maxHours;

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getMyGroups(): Collection
    {
        return $this->myGroups;
    }

    public function addMyGroup(Group $myGroup): static
    {
        if (!$this->myGroups->contains($myGroup)) {
            $this->myGroups->add($myGroup);
            $myGroup->setTeacher($this);
        }

        return $this;
    }

    public function removeMyGroup(Group $myGroup): static
    {
        if ($this->myGroups->removeElement($myGroup)) {
            // set the owning side to null (unless already changed)
            if ($myGroup->getTeacher() === $this) {
                $myGroup->setTeacher(null);
            }
        }

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
     * @return Collection<int, Prestation>
     */
    public function getPrestations(): Collection
    {
        return $this->prestations;
    }

    public function addPrestation(Prestation $prestation): static
    {
        if (!$this->prestations->contains($prestation)) {
            $this->prestations->add($prestation);
            $prestation->setAgent($this);
        }

        return $this;
    }

    public function removePrestation(Prestation $prestation): static
    {
        if ($this->prestations->removeElement($prestation)) {
            // set the owning side to null (unless already changed)
            if ($prestation->getAgent() === $this) {
                $prestation->setAgent(null);
            }
        }

        return $this;
    }

}
