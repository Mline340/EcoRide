<?php

namespace App\Entity;

use App\Repository\PreferenceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PreferenceRepository::class)]
class Preference
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['preferences:read'])]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['preferences:read'])]
    private ?bool $passager = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['preferences:read'])]
    private ?bool $chauffeur = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['preferences:read'])]
    private ?bool $pasChau = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['preferences:read'])]
    private ?bool $animaux = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['preferences:read'])]
    private ?bool $fumeur = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['preferences:read'])]
    private ?int $NbrPlace = null;

    #[ORM\Column(length: 500, nullable: true)]
    #[Groups(['preferences:read'])]
    private ?string $message = null;

    #[ORM\OneToOne(inversedBy: 'preference', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $Utilisateur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isPassager(): ?bool
    {
        return $this->passager;
    }

    public function setPassager(?bool $passager): static
    {
        $this->passager = $passager;

        return $this;
    }

    public function isChauffeur(): ?bool
    {
        return $this->chauffeur;
    }

    public function setChauffeur(?bool $chauffeur): static
    {
        $this->chauffeur = $chauffeur;

        return $this;
    }

    public function isPasChau(): ?bool
    {
        return $this->pasChau;
    }

    public function setPasChau(?bool $pasChau): static
    {
        $this->pasChau = $pasChau;

        return $this;
    }

    public function isAnimaux(): ?bool
    {
        return $this->animaux;
    }

    public function setAnimaux(?bool $animaux): static
    {
        $this->animaux = $animaux;

        return $this;
    }

    public function isFumeur(): ?bool
    {
        return $this->fumeur;
    }

    public function setFumeur(?bool $fumeur): static
    {
        $this->fumeur = $fumeur;

        return $this;
    }

    public function getNbrPlace(): ?int
    {
        return $this->NbrPlace;
    }

    public function setNbrPlace(?int $NbrPlace): static
    {
        $this->NbrPlace = $NbrPlace;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->Utilisateur;
    }

    public function setUtilisateur(Utilisateur $Utilisateur): static
    {
        $this->Utilisateur = $Utilisateur;

        return $this;
    }
}
