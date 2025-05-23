<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Repository\RolePermissionsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RolePermissionsRepository::class)]
class RolePermissions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Role::class, inversedBy: 'rolePermissions')]
    #[ORM\JoinColumn(nullable: false)]
    private $role;

    #[ORM\ManyToOne(targetEntity: RolePermission::class, inversedBy: 'rolePermissions')]
    #[ORM\JoinColumn(nullable: false)]
    private $permission;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getPermission(): ?RolePermission
    {
        return $this->permission;
    }

    public function setPermission(?RolePermission $permission): self
    {
        $this->permission = $permission;

        return $this;
    }
}