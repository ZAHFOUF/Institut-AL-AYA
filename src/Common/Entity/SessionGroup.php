<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Repository\SessionGroupRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionGroupRepository::class)]
class SessionGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'sessionGroups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Group $group = null;

    #[ORM\ManyToOne(targetEntity: Session::class, inversedBy: 'sessionGroups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Session $session = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(?Group $group): static
    {
        $this->group = $group;

        return $this;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): static
    {
        $this->session = $session;

        return $this;
    }

    public function addSessionStudent(Student $student)
    {
        $sessionStudent = new SessionStudent();
        $formula = $this->getSession()->getFormula();
        $additionalHours = $this->getSession()->getAdditionalHours();
        
        // Get price from FormulaSessionType based on session type
        $price = 0;
        if ($formula) {
            $sessionType = $this->getSession()->getType();
            $formulaSessionTypes = $formula->getFormulaSessionTypes();
            
            // Find matching formula session type using array_filter
            $matchingTypes = array_filter(
                $formulaSessionTypes->toArray(),
                fn($type) => $type->getType() === $sessionType
            );
            
            if (!empty($matchingTypes)) {
                $formulaSessionType = reset($matchingTypes);
                $price = floatval($formulaSessionType->getPrice());
            }
        }
        
        if ($additionalHours > 0) {
            $hourlyRate = $this->getSession()->getModule()->getId() == 1 ? 6 : 5;
            $price += $additionalHours * $hourlyRate;
        }

        if ($student->isFirst()) {
            $price += 5;
        }
        
        $sessionStudent->setSession($this)
            ->setStudent($student)
            ->setPayed(false)
            ->setAmount($price);
            
        return $sessionStudent;
    }
    
}
