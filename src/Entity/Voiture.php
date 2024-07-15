<?php

namespace App\Entity;

use App\Repository\VoitureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VoitureRepository::class)]
class Voiture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $modele = null;

    #[ORM\Column(length: 255)]
    private ?string $couleur = null;

    #[ORM\Column(length: 255)]
    private ?string $lieu = null;

    #[ORM\Column(length: 255)]
    private ?string $attributs = null;

    #[ORM\Column]
    private ?float $prixParJour = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'voiture')]
    private Collection $image;

    #[ORM\ManyToOne(inversedBy: 'voitures')]
    private ?Marque $marque = null;

    #[ORM\ManyToOne(inversedBy: 'voiture')]
    private ?Categorie $categorie = null;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'voiture')]
    private Collection $reservations;

    #[ORM\Column]
    private ?bool $statutReservation = null;

    /**
     * @var Collection<int, FicheMaintenance>
     */
    #[ORM\OneToMany(targetEntity: FicheMaintenance::class, mappedBy: 'voiture')]
    private Collection $ficheMaintenances;

    public function __construct()
    {
        $this->image = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->ficheMaintenances = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(string $modele): static
    {
        $this->modele = $modele;

        return $this;
    }

    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    public function setCouleur(string $couleur): static
    {
        $this->couleur = $couleur;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getAttributs(): ?string
    {
        return $this->attributs;
    }

    public function setAttributs(string $attributs): static
    {
        $this->attributs = $attributs;

        return $this;
    }

    public function getPrixParJour(): ?float
    {
        return $this->prixParJour;
    }

    public function setPrixParJour(float $prixParJour): static
    {
        $this->prixParJour = $prixParJour;

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

    /**
     * @return Collection<int, Image>
     */
    public function getImage(): Collection
    {
        return $this->image;
    }

    public function addImage(Image $image): static
    {
        if (!$this->image->contains($image)) {
            $this->image->add($image);
            $image->setVoiture($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->image->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getVoiture() === $this) {
                $image->setVoiture(null);
            }
        }

        return $this;
    }

    public function getMarque(): ?Marque
    {
        return $this->marque;
    }

    public function setMarque(?Marque $marque): static
    {
        $this->marque = $marque;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setVoiture($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getVoiture() === $this) {
                $reservation->setVoiture(null);
            }
        }

        return $this;
    }

    public function isStatutReservation(): ?bool
    {
        return $this->statutReservation;
    }

    public function setStatutReservation(bool $statutReservation): static
    {
        $this->statutReservation = $statutReservation;

        return $this;
    }

    /**
     * @return Collection<int, FicheMaintenance>
     */
    public function getFicheMaintenances(): Collection
    {
        return $this->ficheMaintenances;
    }

    public function addFicheMaintenance(FicheMaintenance $ficheMaintenance): static
    {
        if (!$this->ficheMaintenances->contains($ficheMaintenance)) {
            $this->ficheMaintenances->add($ficheMaintenance);
            $ficheMaintenance->setVoiture($this);
        }

        return $this;
    }

    public function removeFicheMaintenance(FicheMaintenance $ficheMaintenance): static
    {
        if ($this->ficheMaintenances->removeElement($ficheMaintenance)) {
            // set the owning side to null (unless already changed)
            if ($ficheMaintenance->getVoiture() === $this) {
                $ficheMaintenance->setVoiture(null);
            }
        }

        return $this;
    }
}
