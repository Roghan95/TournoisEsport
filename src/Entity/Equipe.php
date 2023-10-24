<?php

namespace App\Entity;

use App\Repository\EquipeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipeRepository::class)]
class Equipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nomEquipe = null;

    #[ORM\Column(length: 50)]
    private ?string $zoneGeo = null;

    #[ORM\Column(length: 255)]
    private ?string $logoEquipe = null;

    #[ORM\Column(length: 255)]
    private ?string $banniere = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomEquipe(): ?string
    {
        return $this->nomEquipe;
    }

    public function setNomEquipe(string $nomEquipe): static
    {
        $this->nomEquipe = $nomEquipe;

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

    public function getLogoEquipe(): ?string
    {
        return $this->logoEquipe;
    }

    public function setLogoEquipe(string $logoEquipe): static
    {
        $this->logoEquipe = $logoEquipe;

        return $this;
    }

    public function getBanniere(): ?string
    {
        return $this->banniere;
    }

    public function setBanniere(string $banniere): static
    {
        $this->banniere = $banniere;

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
}
