<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Repository\SpecificNeedsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpecificNeedsRepository::class)]
class SpecificNeeds
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, StudentSpecificNeeds>
     */
    #[ORM\OneToMany(targetEntity: StudentSpecificNeeds::class, mappedBy: 'specificNeed')]
    private Collection $studentSpecificNeeds;

    public function __construct()
    {
        $this->studentSpecificNeeds = new ArrayCollection();
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
     * @return Collection<int, StudentSpecificNeeds>
     */
    public function getStudentSpecificNeeds(): Collection
    {
        return $this->studentSpecificNeeds;
    }

    public function addStudentSpecificNeed(StudentSpecificNeeds $studentSpecificNeed): static
    {
        if (!$this->studentSpecificNeeds->contains($studentSpecificNeed)) {
            $this->studentSpecificNeeds->add($studentSpecificNeed);
            $studentSpecificNeed->setSpecificNeed($this);
        }

        return $this;
    }

    public function removeStudentSpecificNeed(StudentSpecificNeeds $studentSpecificNeed): static
    {
        if ($this->studentSpecificNeeds->removeElement($studentSpecificNeed)) {
            // set the owning side to null (unless already changed)
            if ($studentSpecificNeed->getSpecificNeed() === $this) {
                $studentSpecificNeed->setSpecificNeed(null);
            }
        }

        return $this;
    }

   

}
