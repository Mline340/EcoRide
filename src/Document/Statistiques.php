<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(collection: "statistiques")]
class Statistique
{
    #[MongoDB\Id]
    private string $id;

    #[MongoDB\Field(type: "date")]
    private \DateTime $date;

    #[MongoDB\Field(type: "int")]
    private int $nombreCovoiturages;

    #[MongoDB\Field(type: "float")]
    private float $creditsGagnes;

    // Getters et Setters
    public function getId(): string { return $this->id; }

    public function getDate(): \DateTime { return $this->date; }
    public function setDate(\DateTime $date): self { $this->date = $date; return $this; }

    public function getNombreCovoiturages(): int { return $this->nombreCovoiturages; }
    public function setNombreCovoiturages(int $nombre): self { $this->nombreCovoiturages = $nombre; return $this; }

    public function getCreditsGagnes(): float { return $this->creditsGagnes; }
    public function setCreditsGagnes(float $credits): self { $this->creditsGagnes = $credits; return $this; }
}