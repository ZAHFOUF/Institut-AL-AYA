<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Entity\RoleAgent;
use Doctrine\ORM\Mapping as ORM;
use AlAya\Common\Repository\RoleRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
class Role
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string',length: 100)]
    private $name;

    #[ORM\Column(type: 'string',length: 100)]
    private $label;

    #[ORM\Column(type: 'string',length: 100, nullable: true)]
    private $description;

    #[ORM\OneToMany(mappedBy: 'role', targetEntity: RoleAgent::class)]
    private $roleAgents;

    #[ORM\OneToMany(mappedBy: 'role', targetEntity: RolePermissions::class)]
    private $rolePermissions;

    public function __construct()
    {
        $this->roleAgents = new ArrayCollection();
        $this->rolePermissions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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
            $roleAgent->setRole($this);
        }

        return $this;
    }

    public function removeRoleAgent(RoleAgent $roleAgent): self
    {
        if ($this->roleAgents->removeElement($roleAgent)) {
            // set the owning side to null (unless already changed)
            if ($roleAgent->getRole() === $this) {
                $roleAgent->setRole(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RolePermissions>
     */
    public function getRolePermissions(): Collection
    {
        return $this->rolePermissions;
    }

    public function addRolePermission(RolePermissions $rolePermission): self
    {
        if (!$this->rolePermissions->contains($rolePermission)) {
            $this->rolePermissions[] = $rolePermission;
            $rolePermission->setRole($this);
        }

        return $this;
    }

    public function removeRolePermission(RolePermissions $rolePermission): self
    {
        if ($this->rolePermissions->removeElement($rolePermission)) {
            // set the owning side to null (unless already changed)
            if ($rolePermission->getRole() === $this) {
                $rolePermission->setRole(null);
            }
        }

        return $this;
    }
}