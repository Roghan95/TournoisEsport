<?php

namespace App\Entity;

use App\Repository\TournoiRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TournoiRepository::class)]
class Tournoi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nomTournoi = null;

    #[ORM\Column(length: 100)]
    private ?string $nomOrganisation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(length: 100)]
    private ?string $zoneGeo = null;

    #[ORM\Column]
    private ?int $nbJoueurMax = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $banniereTr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lienTwitch = null;

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

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getZoneGeo(): ?string
    {
        return $this->zoneGeo;
    }

    public function setZoneGeo(string $zoneGeo): static
    {
        $this->zoneGeo = $zoneGeo;

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
}
