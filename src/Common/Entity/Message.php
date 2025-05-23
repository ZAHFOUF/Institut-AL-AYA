<?php

namespace AlAya\Common\Entity;

use AlAya\Common\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Group $groupe = null;

    #[ORM\Column]
    private ?int $senderId = null;

    #[ORM\Column(length: 255)]
    private ?string $senderType = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $sendedAt = null;

    #[ORM\Column(length: 1000)]
    private ?string $centent = null;

    #[ORM\Column(nullable:true)]
    private ?array $viewers = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSenderId(): ?int
    {
        return $this->senderId;
    }

    public function setSenderId(int $senderId): static
    {
        $this->senderId = $senderId;

        return $this;
    }

    public function getSenderType(): ?string
    {
        return $this->senderType;
    }

    public function setSenderType(string $senderType): static
    {
        $this->senderType = $senderType;

        return $this;
    }

    public function getSendedAt(): ?\DateTimeImmutable
    {
        return $this->sendedAt;
    }

    public function setSendedAt(\DateTimeImmutable $sendedAt): static
    {
        $this->sendedAt = $sendedAt;

        return $this;
    }

    public function getCentent(): ?string
    {
        return $this->centent;
    }

    public function setCentent(string $centent): static
    {
        $this->centent = $centent;

        return $this;
    }

    public function getViewers(): ?array
    {
        return $this->viewers;
    }

    public function setViewers(?array $viewers): static
    {
        $this->viewers = $viewers;

        return $this;
    }
    public function addViewer(int $viewer): static
    {
        if ($this->viewers === null) {
            $this->viewers = [];
        }
        if (!in_array($viewer, $this->viewers)) {
            $this->viewers[] = $viewer;
        }

        return $this;
    }
}
