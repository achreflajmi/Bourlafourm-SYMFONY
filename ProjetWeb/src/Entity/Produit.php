<?php

namespace App\Entity;
use App\Entity\Categorie;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\ProduitRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
#[ORM\GeneratedValue]
#[ORM\Column(type: 'integer')]
private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom est requis")]
    #[Assert\Type(type: "string", message: "Le nom doit être une chaîne de caractères")]
    public ?string $nom_prod = null;

    #[ORM\Column(type: "float")]
    #[Assert\NotBlank(message: "Le prix est requis")]
    #[Assert\Type(type: "float", message: "Le prix doit être un nombre décimal")]
    public ?float $prix_prod = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description est requise")]
    #[Assert\Type(type: "string", message: "La description doit être une chaîne de caractères")]
    public ?string $description_prod = null;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "La quantité est requise")]
    #[Assert\Type(type: "integer", message: "La quantité doit être un nombre entier")]
    public ?int $quantite_prod = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'image est requise")]
    public ?string $image_prod = null;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Categorie", inversedBy: "produits")]
    #[ORM\JoinColumn(name: "id_categorie", referencedColumnName: "id", nullable: false)]
    #[Assert\NotBlank(message: "La catégorie est requise")]

    public ?Categorie $categorie = null;

    #[ORM\OneToMany(targetEntity: Ratings::class, mappedBy: "produit")]
    private Collection $ratings;


    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;
        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomProd(): ?string
    {
        return $this->nom_prod;
    }

    public function setNomProd(?string $nom_prod): self
    {
        $this->nom_prod = $nom_prod;

        return $this;
    }
   
    public function getPrixProd(): ?float
    {
        return $this->prix_prod;
    }

    public function setPrixProd(?float $prix_prod): self
    {
        $this->prix_prod = $prix_prod;

        return $this;
    }

    public function getDescriptionProd(): ?string
    {
        return $this->description_prod;
    }

    public function setDescriptionProd(?string $description_prod): self
    {
        $this->description_prod = $description_prod;

        return $this;
    }

    public function getQuantiteProd(): ?int
    {
        return $this->quantite_prod;
    }

    public function setQuantiteProd(?int $quantite_prod): self
    {
        $this->quantite_prod = $quantite_prod;

        return $this;
    }

    public function getImageProd(): ?string
    {
        return $this->image_prod;
    }

    public function setImageProd(?string $image_prod): self
    {
        $this->image_prod = $image_prod;

        return $this;
    }

  public function __construct()
    {
        $this->ratings = new ArrayCollection();
    }

    /**
     * @return Collection|Ratings[]
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }
    public function setRating(?float $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function addRating(Ratings $rating): self
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings[] = $rating;
            $rating->setProduit($this);
        }

        return $this;
    }
  
}
