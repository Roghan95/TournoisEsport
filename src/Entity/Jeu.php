<?php

namespace App\Entity;

use App\Repository\JeuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: JeuRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
class Jeu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nomJeu = null;

    #[Vich\UploadableField(mapping: 'jeu_logo', fileNameProperty: 'logo')]
    private ?File $logoFile = null;

    #[ORM\Column(length: 255)]
    private ?string $logo = null;

    #[ORM\OneToMany(mappedBy: 'jeu', targetEntity: Tournoi::class)]
    private Collection $tournois;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->tournois = new ArrayCollection();
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

    public function getNomJeu(): ?string
    {
        return $this->nomJeu;
    }

    public function setNomJeu(string $nomJeu): static
    {
        $this->nomJeu = $nomJeu;

        return $this;
    }

    public function getLogoFile(): ?File
    {
        return $this->logoFile;
    }

    public function setLogoFile(?File $logoFile = null): void
    {
        $this->logoFile = $logoFile;

        if (null !== $logoFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }


    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @return Collection<int, Tournoi>
     */
    public function getTournois(): Collection
    {
        return $this->tournois;
    }

    public function addTournoi(Tournoi $tournoi): static
    {
        if (!$this->tournois->contains($tournoi)) {
            $this->tournois->add($tournoi);
            $tournoi->setJeu($this);
        }

        return $this;
    }

    public function removeTournoi(Tournoi $tournoi): static
    {
        if ($this->tournois->removeElement($tournoi)) {
            // set the owning side to null (unless already changed)
            if ($tournoi->getJeu() === $this) {
                $tournoi->setJeu(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->nomJeu;
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
}
