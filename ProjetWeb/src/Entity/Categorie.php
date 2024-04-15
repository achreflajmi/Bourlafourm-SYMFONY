<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de la catégorie est requis")]
    #[Assert\Type(type: "string", message: "Le nom de la catégorie doit être une chaîne de caractères")]
    public ?string $nom_categorie = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description de la catégorie est requise")]
    #[Assert\Type(type: "string", message: "La description de la catégorie doit être une chaîne de caractères")]
    public ?string $description_categorie = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le type de la catégorie est requis")]
    #[Assert\Type(type: "string", message: "Le type de la catégorie doit être une chaîne de caractères")]
    public ?string $type_categorie = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCategorie(): ?string
    {
        return $this->nom_categorie;
    }

    public function setNomCategorie(?string $nom_categorie): self
    {
        $this->nom_categorie = $nom_categorie;

        return $this;
    }

    public function getDescriptionCategorie(): ?string
    {
        return $this->description_categorie;
    }

    public function setDescriptionCategorie(?string $description_categorie): self
    {
        $this->description_categorie = $description_categorie;

        return $this;
    }

    public function getTypeCategorie(): ?string
    {
        return $this->type_categorie;
    }

    public function setTypeCategorie(?string $type_categorie): self
    {
        $this->type_categorie = $type_categorie;

        return $this;
    }
}
