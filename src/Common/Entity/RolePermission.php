<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Repository\RolePermissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RolePermissionRepository::class)]
class RolePermission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string',length: 100, unique: true)]
    private $name;

    #[ORM\Column(type: 'string',length: 100)]
    private $label;

    #[ORM\Column(type: 'string',length: 255, nullable: true)]
    private $description;

    #[ORM\OneToMany(mappedBy: 'permission', targetEntity: RolePermissions::class)]
    private $rolePermissions;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $module = null;

    public function __construct()
    {
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
            $rolePermission->setPermission($this);
        }

        return $this;
    }

    public function removeRolePermission(RolePermissions $rolePermission): self
    {
        if ($this->rolePermissions->removeElement($rolePermission)) {
            // set the owning side to null (unless already changed)
            if ($rolePermission->getPermission() === $this) {
                $rolePermission->setPermission(null);
            }
        }

        return $this;
    }

    public function getModule(): ?string
    {
        return $this->module;
    }

    public function setModule(?string $module): self
    {
        $this->module = $module;

        return $this;
    }
}