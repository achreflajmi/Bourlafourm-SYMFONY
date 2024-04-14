<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 255)]
    public ?string $nom_categorie = null;

    #[ORM\Column(length: 255)]
    public ?string $description_categorie = null;

    #[ORM\Column(length: 255)]
    public ?string $type_categorie = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCategorie(): ?string
    {
        return $this->nom_categorie;
    }

    public function setNomCategorie(string $nom_categorie): static
    {
        $this->nom_categorie = $nom_categorie;

        return $this;
    }

    public function getDescriptionCategorie(): ?string
    {
        return $this->description_categorie;
    }

    public function setDescriptionCategorie(string $description_categorie): static
    {
        $this->description_categorie = $description_categorie;

        return $this;
    }

    public function getTypeCategorie(): ?string
    {
        return $this->type_categorie;
    }

    public function setTypeCategorie(string $type_categorie): static
    {
        $this->type_categorie = $type_categorie;

        return $this;
    }
}
