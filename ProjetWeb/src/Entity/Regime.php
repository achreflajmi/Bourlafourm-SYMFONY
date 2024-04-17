<?php

namespace App\Entity;

use App\Repository\RegimeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RegimeRepository::class)]
class Regime
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $petitdej = null;

    #[ORM\Column(length: 255)]
    private ?string $colpdej = null;

    #[ORM\Column(length: 255)]
    private ?string $dej = null;

    #[ORM\Column(length: 255)]
    private ?string $coldej = null;

    #[ORM\Column(length: 255)]
    private ?string $diner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPetitdej(): ?string
    {
        return $this->petitdej;
    }

    public function setPetitdej(string $petitdej): static
    {
        $this->petitdej = $petitdej;

        return $this;
    }

    public function getColpdej(): ?string
    {
        return $this->colpdej;
    }

    public function setColpdej(string $colpdej): static
    {
        $this->colpdej = $colpdej;

        return $this;
    }

    public function getDej(): ?string
    {
        return $this->dej;
    }

    public function setDej(string $dej): static
    {
        $this->dej = $dej;

        return $this;
    }

    public function getColdej(): ?string
    {
        return $this->coldej;
    }

    public function setColdej(string $coldej): static
    {
        $this->coldej = $coldej;

        return $this;
    }

    public function getDiner(): ?string
    {
        return $this->diner;
    }

    public function setDiner(string $diner): static
    {
        $this->diner = $diner;

        return $this;
    }
}
