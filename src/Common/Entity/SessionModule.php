<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Repository\SessionModuleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionModuleRepository::class)]
class SessionModule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sessionModules')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Module $session = null;

    #[ORM\ManyToOne(inversedBy: 'sessionModules')]
    private ?Module $module = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSession(): ?Module
    {
        return $this->session;
    }

    public function setSession(?Module $session): static
    {
        $this->session = $session;

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
}
