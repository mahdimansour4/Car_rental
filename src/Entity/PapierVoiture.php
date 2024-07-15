<?php

namespace App\Entity;

use App\Repository\PapierVoitureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PapierVoitureRepository::class)]
class PapierVoiture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFinVignette = null;

    #[ORM\Column]
    private ?float $prixVignette = null;

    #[ORM\Column(length: 255)]
    private ?string $dateFinAssurance = null;

    #[ORM\Column]
    private ?float $prixAssurance = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Voiture $voiture = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateFinVignette(): ?\DateTimeInterface
    {
        return $this->dateFinVignette;
    }

    public function setDateFinVignette(\DateTimeInterface $dateFinVignette): static
    {
        $this->dateFinVignette = $dateFinVignette;

        return $this;
    }

    public function getPrixVignette(): ?float
    {
        return $this->prixVignette;
    }

    public function setPrixVignette(float $prixVignette): static
    {
        $this->prixVignette = $prixVignette;

        return $this;
    }

    public function getDateFinAssurance(): ?string
    {
        return $this->dateFinAssurance;
    }

    public function setDateFinAssurance(string $dateFinAssurance): static
    {
        $this->dateFinAssurance = $dateFinAssurance;

        return $this;
    }

    public function getPrixAssurance(): ?float
    {
        return $this->prixAssurance;
    }

    public function setPrixAssurance(float $prixAssurance): static
    {
        $this->prixAssurance = $prixAssurance;

        return $this;
    }

    public function getVoiture(): ?Voiture
    {
        return $this->voiture;
    }

    public function setVoiture(?Voiture $voiture): static
    {
        $this->voiture = $voiture;

        return $this;
    }
}
