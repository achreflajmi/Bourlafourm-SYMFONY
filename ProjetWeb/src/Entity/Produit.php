<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProduitRepository")
 */

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\ManyToOne(targetEntity: "App\Entity\Categorie", inversedBy: "produits")]     
    #[ORM\JoinColumn(name: "nom_categorie", referencedColumnName: "id", nullable: true)]

     /**
     * @ORM\OneToMany(targetEntity=Panier::class, mappedBy="produit")
     */
    
    #[ORM\Column]

    public ?int $id = null;

    #[ORM\Column(length: 255)]
    
    private ?string $nom_prod = null;

    #[ORM\Column]
    public ?float $prix_prod = null;

    #[ORM\Column(length: 255)]
    public ?string $description_prod = null;

    #[ORM\Column]
    private ?int $quantite_prod = null;

    #[ORM\Column(length: 255)]
    private ?string $image_prod = null;

     
    #[ORM\Column]
    public ?int $nom_categorie = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomProd(): ?string
    {
        return $this->nom_prod;
    }

    public function setNomProd(string $nom_prod): static
    {
        $this->nom_prod = $nom_prod;

        return $this;
    }

    public function getPrixProd(): ?float
    {
        return $this->prix_prod;
    }

    public function setPrixProd(float $prix_prod): static
    {
        $this->prix_prod = $prix_prod;

        return $this;
    }

    public function getDescriptionProd(): ?string
    {
        return $this->description_prod;
    }

    public function setDescriptionProd(string $description_prod): static
    {
        $this->description_prod = $description_prod;

        return $this;
    }

    public function getQuantiteProd(): ?int
    {
        return $this->quantite_prod;
    }

    public function setQuantiteProd(int $quantite_prod): static
    {
        $this->quantite_prod = $quantite_prod;

        return $this;
    }

    public function getImageProd(): ?string
    {
        return $this->image_prod;
    }

    public function setImageProd(string $image_prod): static
    {
        $this->image_prod = $image_prod;

        return $this;
    }

    public function getNomCategorie(): ?int
    {
        return $this->nom_categorie;
    }

    public function setNomCategorie(int $nom_categorie): static
    {
        $this->nom_categorie = $nom_categorie;

        return $this;
    }
}
