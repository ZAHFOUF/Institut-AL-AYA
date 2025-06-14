<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Entity\Trait\createdFields;
use AlAya\Common\Repository\PrestationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrestationRepository::class)]
class Prestation
{

    const ACTIVE = 'en cours';
    const WEEKS = 4 ;


    use createdFields ;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'prestations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Programme $programme = null;

    #[ORM\ManyToOne(inversedBy: 'prestations')]
    private ?Agent $agent = null;

    #[ORM\ManyToOne(inversedBy: 'prestations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Formula $formula = null;

    #[ORM\ManyToOne(inversedBy: 'prestations')]
    private ?Group $groupe = null;

    #[ORM\ManyToOne(inversedBy: 'prestations')]
    private ?Student $student = null;

    /**
     * @var Collection<int, PrestationLine>
     */
    #[ORM\OneToMany(targetEntity: PrestationLine::class, mappedBy: 'prestation')]
    private Collection $prestationLines;

    /**
     * @var Collection<int, Session>
     */
    #[ORM\OneToMany(targetEntity: Session::class, mappedBy: 'prestation')]
    private Collection $sessions;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(nullable: true)]
    private ?int $rate = null;

    #[ORM\Column(nullable: true)]
    private ?int $hours = null;

    /**
     * @var Collection<int, Bill>
     */
    #[ORM\OneToMany(targetEntity: Bill::class, mappedBy: 'prestation')]
    private Collection $bills;

    public function __construct()
    {
        $this->prestationLines = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->bills = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProgramme(): ?Programme
    {
        return $this->programme;
    }

    public function setProgramme(?Programme $programme): static
    {
        $this->programme = $programme;

        return $this;
    }

    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    public function setAgent(?Agent $agent): static
    {
        $this->agent = $agent;

        return $this;
    }

    public function getFormula(): ?Formula
    {
        return $this->formula;
    }

    public function setFormula(?Formula $formula): static
    {
        $this->formula = $formula;

        return $this;
    }

    public function getGroupe(): ?Group
    {
        return $this->groupe;
    }

    public function setGroupe(?Group $groupe): static
    {
        $this->groupe = $groupe;

        return $this;
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

    public function getClient() {
        if ($this->student) {
            return $this->student;
        } elseif ($this->groupe) {
            return $this->groupe;
        } else {
            return null;
        }
    }

    public function getClientName(): ?string
    {
       if ($this->getClient()) {
           return $this->getClient()->getFullName(); // Assurez-vous que getFullName() est défini dans Student ou Group
       } else {
           return null; // ou une valeur par défaut si aucun client n'est défini
       }
    }

    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    /**
     * @return Collection<int, PrestationLine>
     */
    public function getPrestationLines(): Collection
    {
        return $this->prestationLines;
    }

    public function addPrestationLine(PrestationLine $prestationLine): static
    {
        if (!$this->prestationLines->contains($prestationLine)) {
            $this->prestationLines->add($prestationLine);
            $prestationLine->setPrestation($this);
        }

        return $this;
    }

    public function removePrestationLine(PrestationLine $prestationLine): static
    {
        if ($this->prestationLines->removeElement($prestationLine)) {
            // set the owning side to null (unless already changed)
            if ($prestationLine->getPrestation() === $this) {
                $prestationLine->setPrestation(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getRate(): ?int
    {
        return $this->rate;
    }

    public function setRate(?int $rate): static
    {
        $this->rate = $rate;

        return $this;
    }

    public function getHours(): ?int
    {
        return $this->hours;
    }

    public function setHours(?int $hours): static
    {
        $this->hours = $hours;

        return $this;
    }

    /**
     * @return Collection<int, Bill>
     */
    public function getBills(): Collection
    {
        return $this->bills;
    }

    public function addBill(Bill $bill): static
    {
        if (!$this->bills->contains($bill)) {
            $this->bills->add($bill);
            $bill->setPrestation($this);
        }

        return $this;
    }

    public function removeBill(Bill $bill): static
    {
        if ($this->bills->removeElement($bill)) {
            // set the owning side to null (unless already changed)
            if ($bill->getPrestation() === $this) {
                $bill->setPrestation(null);
            }
        }

        return $this;
    }

}
