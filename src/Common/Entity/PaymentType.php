<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Repository\PaymentTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentTypeRepository::class)]
class PaymentType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, SessionStudent>
     */
    #[ORM\OneToMany(targetEntity: SessionStudent::class, mappedBy: 'typePay')]
    private Collection $sessionStudents;

    public function __construct()
    {
        $this->sessionStudents = new ArrayCollection();
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
            $sessionStudent->setTypePay($this);
        }

        return $this;
    }

    public function removeSessionStudent(SessionStudent $sessionStudent): static
    {
        if ($this->sessionStudents->removeElement($sessionStudent)) {
            // set the owning side to null (unless already changed)
            if ($sessionStudent->getTypePay() === $this) {
                $sessionStudent->setTypePay(null);
            }
        }

        return $this;
    }
}
