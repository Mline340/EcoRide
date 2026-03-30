<?php

namespace App\Entity;

use App\Repository\CovoiturageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CovoiturageRepository::class)]
class Covoiturage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['covoiturage:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['covoiturage:read'])]
    private ?\DateTime $date_depart = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Groups(['covoiturage:read'])]
    private ?\DateTime $heure_depart = null;

    #[ORM\Column(length: 50)]
    #[Groups(['covoiturage:read'])]
    private ?string $lieu_depart = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Groups(['covoiturage:read'])]
    private ?\DateTime $heure_arrivee = null;

    #[ORM\Column(length: 50)]
    #[Groups(['covoiturage:read'])]
    private ?string $lieu_arrivee = null;

    #[ORM\Column(length: 50)]
    #[Groups(['covoiturage:read'])]
    private ?string $info = null;

    #[ORM\Column]
    #[Groups(['covoiturage:read'])]
    private ?float $prix_personne = null;

    #[ORM\Column]
    #[Groups(['covoiturage:read'])]
    private ?int $nb_places = null;

    /**
     * @var Collection<int, Utilisateur>
     */
    #[ORM\ManyToOne(inversedBy: 'covoiturages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;


    #[ORM\ManyToOne(inversedBy: 'covoiturage')]
    private ?Voiture $voiture = null;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'covoiturage')]
    private Collection $reservations;

    /**
     * @var Collection<int, Transaction>
     */
    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'covoiturage')]
    private Collection $transactions;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->transactions = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDepart(): ?\DateTime
    {
        return $this->date_depart;
    }

    public function setDateDepart(\DateTime $date_depart): static
    {
        $this->date_depart = $date_depart;

        return $this;
    }

    public function getHeureDepart(): ?\DateTime
    {
        return $this->heure_depart;
    }

    public function setHeureDepart(\DateTime $heure_depart): static
    {
        $this->heure_depart = $heure_depart;

        return $this;
    }

    public function getLieuDepart(): ?string
    {
        return $this->lieu_depart;
    }

    public function setLieuDepart(string $lieu_depart): static
    {
        $this->lieu_depart = $lieu_depart;

        return $this;
    }


    public function getHeureArrivee(): ?\DateTime
    {
        return $this->heure_arrivee;
    }

    public function setHeureArrivee(\DateTime $heure_arrivee): static
    {
        $this->heure_arrivee = $heure_arrivee;

        return $this;
    }

    public function getLieuArrivee(): ?string
    {
        return $this->lieu_arrivee;
    }

    public function setLieuArrivee(string $lieu_arrivee): static
    {
        $this->lieu_arrivee = $lieu_arrivee;

        return $this;
    }

    public function getInfo(): ?string
    {
        return $this->info;
    }

    public function setInfo(string $info): static
    {
        $this->info = $info;

        return $this;
    }

    public function getPrixPersonne(): ?float
    {
        return $this->prix_personne;
    }

    public function setPrixPersonne(float $prix_personne): static
    {
        $this->prix_personne = $prix_personne;

        return $this;
    }

    public function getNbPlaces(): ?int 
    { 
        return $this->nb_places; 
    }
    public function setNbPlaces(int $nb_places): static 
    {
         $this->nb_places = $nb_places; return $this; 
    }

    /**
     * @return Collection<int, Utilisateur>
     */
    public function getUtilisateur(): ?Utilisateur
    {
    return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
    $this->utilisateur = $utilisateur;
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
            $reservation->setCovoiturage($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getCovoiturage() === $this) {
                $reservation->setCovoiturage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): static
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setCovoiturage($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): static
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getCovoiturage() === $this) {
                $transaction->setCovoiturage(null);
            }
        }

        return $this;
    }
}
