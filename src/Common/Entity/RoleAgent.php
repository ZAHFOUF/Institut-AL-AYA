<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Entity\Agent;
use Doctrine\ORM\Mapping as ORM;
use AlAya\Common\Repository\RoleAgentRepository;

#[ORM\Entity(repositoryClass: RoleAgentRepository::class)]
class RoleAgent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Agent::class, inversedBy: 'roleAgents')]
    #[ORM\JoinColumn(nullable: false)]
    private $agent;

    #[ORM\ManyToOne(targetEntity: Role::class, inversedBy: 'roleAgents')]
    #[ORM\JoinColumn(nullable: false)]
    private $role;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    public function setAgent(?Agent $agent): self
    {
        $this->agent = $agent;

        return $this;
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
}