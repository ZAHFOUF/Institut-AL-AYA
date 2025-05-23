<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Repository\GroupRequestTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupRequestTypeRepository::class)]
class GroupRequestType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, SessionRequest>
     */
    #[ORM\OneToMany(targetEntity: SessionRequest::class, mappedBy: 'groupType')]
    private Collection $sessionRequests;

    public function __construct()
    {
        $this->sessionRequests = new ArrayCollection();
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
            $sessionRequest->setGroupType($this);
        }

        return $this;
    }

    public function removeSessionRequest(SessionRequest $sessionRequest): static
    {
        if ($this->sessionRequests->removeElement($sessionRequest)) {
            // set the owning side to null (unless already changed)
            if ($sessionRequest->getGroupType() === $this) {
                $sessionRequest->setGroupType(null);
            }
        }

        return $this;
    }
}
