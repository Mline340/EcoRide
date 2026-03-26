<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Id;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Field;

#[Document(collection: "credits_par_jour")]
class CreditParJour
{
    #[Id]
    private string $id;

    #[Field(type: "date")]
    private \DateTime $date;

    #[Field(type: "int")]
    private int $nombreCredits;

    public function getId(): string { return $this->id; }

    public function getDate(): \DateTime { return $this->date; }
    public function setDate(\DateTime $date): self { $this->date = $date; return $this; }

    public function getNombreCredits(): int { return $this->nombreCredits; }
    public function setNombreCredits(int $credits): self { $this->nombreCredits = $credits; return $this; }
}