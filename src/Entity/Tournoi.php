<?php

namespace App\Entity;

use App\Repository\TournoiRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Entity\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TournoiRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
class Tournoi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank (message: "Le nom du tournoi ne peut pas être vide")]
    private ?string $nomTournoi = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank (message: "Le nom de l'organisation ne peut pas être vide")]
    private ?string $nomOrganisation = null;

    #[Vich\UploadableField(mapping: 'tournoi_image', fileNameProperty: 'logoTournoi')]
    #[Assert\NotBlank (message: "Le logo du tournoi ne peut pas être vide")]
    private ?File $imageFile = null;  

    #[ORM\Column]
    #[Assert\NotBlank (message: "Vous devez renseigner la date de début")]
    private ?\DateTimeImmutable $dateDebut = null;

    #[ORM\Column]
    #[Assert\NotBlank (message: "Vous devez renseigner la date de fin")]
    private ?\DateTimeImmutable $dateFin = null;

    #[ORM\Column]
    #[Assert\NotBlank (message: "Vous devez renseigner le nombre de joueurs maximum")]
    private ?int $nbJoueurMax = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $banniereTr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lienTwitch = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'tournoi', targetEntity: GameMatch::class, orphanRemoval: true)]
    private Collection $gameMatches;

    #[ORM\ManyToOne(inversedBy: 'tournois')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Jeu $jeu = null;

    #[ORM\ManyToOne(inversedBy: 'mesTournois')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $organisateur = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logoTournoi = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $reglement = null;

    public function __construct()
    {
        $this->gameMatches = new ArrayCollection();
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

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

    public function getNomTournoi(): ?string
    {
        return $this->nomTournoi;
    }

    public function setNomTournoi(string $nomTournoi): static
    {
        $this->nomTournoi = $nomTournoi;

        return $this;
    }

    public function getNomOrganisation(): ?string
    {
        return $this->nomOrganisation;
    }

    public function setNomOrganisation(string $nomOrganisation): static
    {
        $this->nomOrganisation = $nomOrganisation;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeImmutable
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeImmutable $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeImmutable
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeImmutable $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getNbJoueurMax(): ?int
    {
        return $this->nbJoueurMax;
    }

    public function setNbJoueurMax(int $nbJoueurMax): static
    {
        $this->nbJoueurMax = $nbJoueurMax;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getBanniereTr(): ?string
    {
        return $this->banniereTr;
    }

    public function setBanniereTr(string $banniereTr): static
    {
        $this->banniereTr = $banniereTr;

        return $this;
    }

    public function getLienTwitch(): ?string
    {
        return $this->lienTwitch;
    }

    public function setLienTwitch(?string $lienTwitch): static
    {
        $this->lienTwitch = $lienTwitch;

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

    /**
     * @return Collection<int, GameMatch>
     */
    public function getGameMatches(): Collection
    {
        return $this->gameMatches;
    }

    public function addGameMatch(GameMatch $gameMatch): static
    {
        if (!$this->gameMatches->contains($gameMatch)) {
            $this->gameMatches->add($gameMatch);
            $gameMatch->setTournoi($this);
        }

        return $this;
    }

    public function removeGameMatch(GameMatch $gameMatch): static
    {
        if ($this->gameMatches->removeElement($gameMatch)) {
            // set the owning side to null (unless already changed)
            if ($gameMatch->getTournoi() === $this) {
                $gameMatch->setTournoi(null);
            }
        }

        return $this;
    }

    public function getJeu(): ?Jeu
    {
        return $this->jeu;
    }

    public function setJeu(?Jeu $jeu): static
    {
        $this->jeu = $jeu;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getNomTournoi();
    }

    public function getOrganisateur(): ?Utilisateur
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?Utilisateur $organisateur): static
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    public function getLogoTournoi(): ?string
    {
        return $this->logoTournoi;
    }

    public function setLogoTournoi(?string $logoTournoi): static
    {
        $this->logoTournoi = $logoTournoi;

        return $this;
    }

    public function getReglement(): ?string
    {
        return $this->reglement;
    }

    public function setReglement(?string $reglement): static
    {
        $this->reglement = $reglement;

        return $this;
    }
}
