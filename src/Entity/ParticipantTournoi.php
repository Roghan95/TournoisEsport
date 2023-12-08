<?php

namespace App\Entity;

use App\Repository\ParticipantTournoiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipantTournoiRepository::class)]
#[ORM\UniqueConstraint(name: 'tournoi_participant', columns: ['utilisateur_id', 'tournoi_id'])]
#[ORM\HasLifecycleCallbacks]
class ParticipantTournoi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'participantTournois')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tournoi $tournoi = null;

    #[ORM\ManyToOne(inversedBy: 'participantTournois')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(length: 100)]
    private ?string $inGamePseudo = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $nomDiscord = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne]
    private ?Equipe $equipe = null;

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

    public function getTournoi(): ?Tournoi
    {
        return $this->tournoi;
    }

    public function setTournoi(?Tournoi $tournoi): static
    {
        $this->tournoi = $tournoi;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getInGamePseudo(): ?string
    {
        return $this->inGamePseudo;
    }

    public function setInGamePseudo(string $inGamePseudo): static
    {
        $this->inGamePseudo = $inGamePseudo;

        return $this;
    }

    public function getNomDiscord(): ?string
    {
        return $this->nomDiscord;
    }

    public function setNomDiscord(string $nomDiscord): static
    {
        $this->nomDiscord = $nomDiscord;

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
        return $this->getInGamePseudo();
    }

    public function getEquipe(): ?Equipe
    {
        return $this->equipe;
    }

    public function setEquipe(?Equipe $equipe): static
    {
        $this->equipe = $equipe;

        return $this;
    }
}
