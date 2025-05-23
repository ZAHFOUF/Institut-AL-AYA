<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Repository\StudentLanguageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentLanguageRepository::class)]
class StudentLanguage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'studentLanguages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Student $student = null;

    #[ORM\ManyToOne(targetEntity: Language::class, inversedBy: 'studentLanguages')]
    private ?Language $language = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'language')]
    private Collection $studentLanguages;

    public function __construct()
    {
        $this->studentLanguages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): static
    {
        $this->student = $student;

        return $this;
    }

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(?Language $language): static
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getStudentLanguages(): Collection
    {
        return $this->studentLanguages;
    }

    public function addStudentLanguage(self $studentLanguage): static
    {
        if (!$this->studentLanguages->contains($studentLanguage)) {
            $this->studentLanguages->add($studentLanguage);
            $studentLanguage->setLanguage($this);
        }

        return $this;
    }

    public function removeStudentLanguage(self $studentLanguage): static
    {
        if ($this->studentLanguages->removeElement($studentLanguage)) {
            // set the owning side to null (unless already changed)
            if ($studentLanguage->getLanguage() === $this) {
                $studentLanguage->setLanguage(null);
            }
        }

        return $this;
    }
}
