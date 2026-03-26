<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Id;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Field;

#[Document(collection: "credits_total")]
class CreditTotal
{
    #[Id]
    private string $id;

    #[Field(type: "date")]
    private \DateTime $dateMiseAJour;

    #[Field(type: "int")]
    private float $totalCreditsGagnes;

    public function getId(): string { return $this->id; }

    public function getDateMiseAJour(): \DateTime { return $this->dateMiseAJour; }
    public function setDateMiseAJour(\DateTime $date): self { $this->dateMiseAJour = $date; return $this; }

    public function getTotalCreditsGagnes(): int { return $this->totalCreditsGagnes; }
    public function setTotalCreditsGagnes(int $total): self { $this->totalCreditsGagnes = $total; return $this; }
}