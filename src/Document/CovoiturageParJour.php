<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Id;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Field;

#[Document(collection: "covoiturages_par_jour")]
class CovoiturageParJour
{
    #[Id]
    private string $id;

    #[Field(type: "date")]
    private \DateTime $date;

    #[Field(type: "int")]
    private int $nombreCovoiturages;

    public function getId(): string { return $this->id; }

    public function getDate(): \DateTime { return $this->date; }
    public function setDate(\DateTime $date): self { $this->date = $date; return $this; }

    public function getNombreCovoiturages(): int { return $this->nombreCovoiturages; }
    public function setNombreCovoiturages(int $nombre): self { $this->nombreCovoiturages = $nombre; return $this; }
}