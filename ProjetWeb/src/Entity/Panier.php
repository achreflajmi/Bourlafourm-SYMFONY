<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{   #[ORM\ManyToOne(targetEntity: "App\Entity\Produit", inversedBy: "paniers")]
    #[ORM\JoinColumn(name: "id_prod", referencedColumnName: "id", nullable: true)]



    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_user = null;

    #[ORM\Column]
    private ?float $total_panier = null;

    #[ORM\Column]
    private ?int $id_prod = null;

    #[ORM\Column]
    private ?int $quantite_panier = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_prod = null;

    #[ORM\Column]
    private ?float $prix_prod = null;

    #[ORM\Column(length: 255)]
    private ?string $image_prod = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    public function setIdUser(int $id_user): static
    {
        $this->id_user = $id_user;

        return $this;
    }

    public function getTotalPanier(): ?float
    {
        return $this->total_panier;
    }

    public function setTotalPanier(float $total_panier): static
    {
        $this->total_panier = $total_panier;

        return $this;
    }

    public function getIdProd(): ?int
    {
        return $this->id_prod;
    }

    public function setIdProd(int $id_prod): static
    {
        $this->id_prod = $id_prod;

        return $this;
    }

    public function getQuantitePanier(): ?int
    {
        return $this->quantite_panier;
    }

    public function setQuantitePanier(int $quantite_panier): static
    {
        $this->quantite_panier = $quantite_panier;

        return $this;
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

    public function getImageProd(): ?string
    {
        return $this->image_prod;
    }

    public function setImageProd(string $image_prod): static
    {
        $this->image_prod = $image_prod;

        return $this;
    }
}
