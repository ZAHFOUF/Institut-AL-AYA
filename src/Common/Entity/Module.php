<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Repository\ModuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModuleRepository::class)]
class Module
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, SessionModule>
     */
    #[ORM\OneToMany(targetEntity: SessionModule::class, mappedBy: 'session')]
    private Collection $sessionModules;

    /**
     * @var Collection<int, Formula>
     */
    #[ORM\OneToMany(targetEntity: Formula::class, mappedBy: 'module')]
    private Collection $formulas;

    /**
     * @var Collection<int, Session>
     */
    #[ORM\OneToMany(targetEntity: Session::class, mappedBy: 'module')]
    private Collection $sessions;

    /**
     * @var Collection<int, Skill>
     */
    #[ORM\OneToMany(targetEntity: Skill::class, mappedBy: 'module')]
    private Collection $skills;

    /**
     * @var Collection<int, Agent>
     */
    #[ORM\OneToMany(targetEntity: Agent::class, mappedBy: 'module')]
    private Collection $agents;

    /**
     * @var Collection<int, Programme>
     */
    #[ORM\OneToMany(targetEntity: Programme::class, mappedBy: 'module')]
    private Collection $programmes;

    public function __construct()
    {
        $this->sessionModules = new ArrayCollection();
        $this->formulas = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->skills = new ArrayCollection();
        $this->agents = new ArrayCollection();
        $this->programmes = new ArrayCollection();
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

    /**
     * @return Collection<int, SessionModule>
     */
    public function getSessionModules(): Collection
    {
        return $this->sessionModules;
    }

    public function addSessionModule(SessionModule $sessionModule): static
    {
        if (!$this->sessionModules->contains($sessionModule)) {
            $this->sessionModules->add($sessionModule);
            $sessionModule->setSession($this);
        }

        return $this;
    }

    public function removeSessionModule(SessionModule $sessionModule): static
    {
        if ($this->sessionModules->removeElement($sessionModule)) {
            // set the owning side to null (unless already changed)
            if ($sessionModule->getSession() === $this) {
                $sessionModule->setSession(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Formula>
     */
    public function getFormulas(): Collection
    {
        return $this->formulas;
    }

    public function addFormula(Formula $formula): static
    {
        if (!$this->formulas->contains($formula)) {
            $this->formulas->add($formula);
            $formula->setModule($this);
        }

        return $this;
    }

    public function removeFormula(Formula $formula): static
    {
        if ($this->formulas->removeElement($formula)) {
            // set the owning side to null (unless already changed)
            if ($formula->getModule() === $this) {
                $formula->setModule(null);
            }
        }

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
            $session->setModule($this);
        }

        return $this;
    }

    public function removeSession(Session $session): static
    {
        if ($this->sessions->removeElement($session)) {
            // set the owning side to null (unless already changed)
            if ($session->getModule() === $this) {
                $session->setModule(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Skill>
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(Skill $skill): static
    {
        if (!$this->skills->contains($skill)) {
            $this->skills->add($skill);
            $skill->setModule($this);
        }

        return $this;
    }

    public function removeSkill(Skill $skill): static
    {
        if ($this->skills->removeElement($skill)) {
            // set the owning side to null (unless already changed)
            if ($skill->getModule() === $this) {
                $skill->setModule(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Agent>
     */
    public function getAgents(): Collection
    {
        return $this->agents;
    }

    public function addAgent(Agent $agent): static
    {
        if (!$this->agents->contains($agent)) {
            $this->agents->add($agent);
            $agent->setModule($this);
        }

        return $this;
    }

    public function removeAgent(Agent $agent): static
    {
        if ($this->agents->removeElement($agent)) {
            // set the owning side to null (unless already changed)
            if ($agent->getModule() === $this) {
                $agent->setModule(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Programme>
     */
    public function getProgrammes(): Collection
    {
        return $this->programmes;
    }

    public function addProgramme(Programme $programme): static
    {
        if (!$this->programmes->contains($programme)) {
            $this->programmes->add($programme);
            $programme->setModule($this);
        }

        return $this;
    }

    public function removeProgramme(Programme $programme): static
    {
        if ($this->programmes->removeElement($programme)) {
            // set the owning side to null (unless already changed)
            if ($programme->getModule() === $this) {
                $programme->setModule(null);
            }
        }

        return $this;
    }
}
