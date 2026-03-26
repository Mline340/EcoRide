<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface

{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['utilisateur:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['utilisateur:read'])]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    #[Groups(['utilisateur:read'])]
    private ?string $prenom = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Groups(['utilisateur:read'])]
    private ?string $email = null;

    #[ORM\Column(length: 250)]
    #[Groups(['utilisateur:read'])]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    #[Groups(['utilisateur:read'])]
    private ?string $telephone = null;

    #[ORM\Column(length: 50)]
    #[Groups(['utilisateur:read'])]
    private ?string $ville = null;

    #[ORM\Column(length: 50)]
    #[Groups(['utilisateur:read'])]
    private ?string $date_naissance = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['utilisateur:read'])]
    private ?string $photo = null;
    
    #[ORM\Column(length: 50)]
    #[Groups(['utilisateur:read'])]
    private ?string $pseudo = null;

    #[ORM\Column(length: 5)]
    #[Groups(['utilisateur:read'])]
    private ?string $code_postal = null;

    /**
     * @var Collection<int, Voiture>
     */
    #[ORM\OneToMany(targetEntity: Voiture::class, mappedBy: 'utilisateur', orphanRemoval: true)]
    private Collection $voitures;

    #[ORM\ManyToOne(inversedBy: 'utilisateurs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Role $Role = null;

    /**
     * @var Collection<int, Avis>
     */
    #[ORM\ManyToMany(targetEntity: Avis::class, inversedBy: 'utilisateurs')]
    private Collection $Avis;

    /**
     * @var Collection<int, Covoiturage>
     */
    #[ORM\OneToMany(targetEntity: Covoiturage::class, mappedBy: 'utilisateur')]
    private Collection $covoiturages;

    #[ORM\OneToOne(inversedBy: 'utilisateur', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Configuration $configuration = null;

    #[ORM\Column(length: 255)]
    private ?string $apiToken = null;

    #[ORM\OneToOne(mappedBy: 'Utilisateur', cascade: ['persist', 'remove'])]
    private ?Preference $preference = null;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'passager')]
    private Collection $reservations;

    /**
     * @var Collection<int, Transaction>
     */
    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'utilisateur')]
    private Collection $transactions;

    #[ORM\Column]
    private int $credit = 20;

    /**@throws \Exception  */
    public function __construct()
    {
        $this->apiToken = bin2hex(random_bytes(20));
        $this->voitures = new ArrayCollection();
        $this->Avis = new ArrayCollection();
        $this->covoiturages = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->transactions = new ArrayCollection();
    }
    
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getDateNaissance(): ?string
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(string $date_naissance): static
    {
        $this->date_naissance = $date_naissance;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto($photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getCodePostal():  ?string
    {
        return $this->code_postal;
    }

    public function setCodePostal(string $code_postal): static
    {
        $this->code_postal = $code_postal;

        return $this;
    }

    public function getUserIdentifier(): string
    {
    return $this->email; // ou autre champ unique
    }

    public function getRoles(): array
    {
    // Récupère le rôle depuis la relation avec l'entité Role
        if ($this->Role) {
         return [$this->Role->getLibelle()];
    }

    return ['ROLE_USER']; // valeur par défaut
    
    }

    /**
     * @return Collection<int, Voiture>
     */
    public function getVoitures(): Collection
    {
        return $this->voitures;
    }

    public function addVoiture(Voiture $voiture): static
    {
        if (!$this->voitures->contains($voiture)) {
            $this->voitures->add($voiture);
            $voiture->setUtilisateur($this);
        }

        return $this;
    }

    public function removeVoiture(Voiture $voiture): static
    {
        if ($this->voitures->removeElement($voiture)) {
            // set the owning side to null (unless already changed)
            if ($voiture->getUtilisateur() === $this) {
                $voiture->setUtilisateur(null);
            }
        }

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->Role;
    }

    public function setRole(?Role $Role): static
    {
        $this->Role = $Role;

        return $this;
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvis(): Collection
    {
        return $this->Avis;
    }

    public function addAvi(Avis $avi): static
    {
        if (!$this->Avis->contains($avi)) {
            $this->Avis->add($avi);
        }

        return $this;
    }

    public function removeAvi(Avis $avi): static
    {
        $this->Avis->removeElement($avi);

        return $this;
    }

    /**
     * @return Collection<int, Covoiturage>
     */
    public function getCovoiturages(): Collection
    {
        return $this->covoiturages;
    }

    public function addCovoiturage(Covoiturage $covoiturage): static
    {
        if (!$this->covoiturages->contains($covoiturage)) {
            $this->covoiturage->add($covoiturage);
            $covoiturage->setUtilisateur($this); 
        }

        return $this;
    }

    public function removeCovoiturage(Covoiturage $covoiturage): static
    {
    if ($this->covoiturages->removeElement($covoiturage)) {
        if ($covoiturage->getUtilisateur() === $this) {
            $covoiturage->setUtilisateur(null);
        }
    }

    return $this;
    }

    public function getConfiguration(): ?Configuration
    {
        return $this->configuration;
    }

    public function setConfiguration(Configuration $configuration): static
    {
        $this->configuration = $configuration;

        return $this;
    }

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function setApiToken(string $apiToken): static
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    public function eraseCredentials(): void
    {
    // Si tu stockes des données sensibles temporaires, efface-les ici
    }

    public function getPreference(): ?Preference
    {
        return $this->preference;
    }

    public function setPreference(Preference $preference): static
    {
        // set the owning side of the relation if necessary
        if ($preference->getUtilisateur() !== $this) {
            $preference->setUtilisateur($this);
        }

        $this->preference = $preference;

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
            $reservation->setPassager($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getPassager() === $this) {
                $reservation->setPassager(null);
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
            $transaction->setUtilisateur($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): static
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getUtilisateur() === $this) {
                $transaction->setUtilisateur(null);
            }
        }

        return $this;
    }

    public function getCredit(): int
    {
        return $this->credit;
    }

    public function setCredit(int $credit): static
    {
        $this->credit = $credit;
        return $this;
    }
}
