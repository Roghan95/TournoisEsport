<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('message')]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups('message')]
    private ?string $texteMessage = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('message')]
    private ?Room $room = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('message')]
    private ?Utilisateur $expediteur = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('message')]
    private ?Utilisateur $destinataire = null;

    #[ORM\Column]
    #[Groups('message')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups('message')]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->setUpdatedAt(new \DateTimeImmutable());

        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTimeImmutable());
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTexteMessage(): ?string
    {
        return $this->texteMessage;
    }

    public function setTexteMessage(string $texteMessage): static
    {
        $this->texteMessage = $texteMessage;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): static
    {
        $this->room = $room;

        return $this;
    }

    public function getExpediteur(): ?Utilisateur
    {
        return $this->expediteur;
    }

    public function setExpediteur(?Utilisateur $expediteur): static
    {
        $this->expediteur = $expediteur;

        return $this;
    }

    public function getDestinataire(): ?Utilisateur
    {
        return $this->destinataire;
    }

    public function setDestinataire(?Utilisateur $destinataire): static
    {
        $this->destinataire = $destinataire;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getTexteMessage();
    }
}
