<?php

namespace App\Entity;

use App\Repository\CoordonneesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CoordonneesRepository::class)]
class Coordonnees
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"L'objet sexe est obligatoire.")]
    private ?string $sexe = null;

    #[ORM\Column(length: 255)]
  
    private ?string $age = null;

    #[ORM\Column(length: 255)]
 
    private ?string $taille = null;

    #[ORM\Column(length: 255)]

    private ?string $poids = null;

    #[ORM\Column(length: 255)]

    private ?string $imc = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): static
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getAge(): ?string
    {
        return $this->age;
    }

    public function setAge(string $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getTaille(): ?string
    {
        return $this->taille;
    }

    public function setTaille(string $taille): static
    {
        $this->taille = $taille;

        return $this;
    }

    public function getPoids(): ?string
    {
        return $this->poids;
    }

    public function setPoids(string $poids): static
    {
        $this->poids = $poids;

        return $this;
    }

    public function getImc(): ?string
    {
        return $this->imc;
    }

    public function setImc(string $imc): static
    {
        $this->imc = $imc;

        return $this;
    }
    // Méthode pour calculer l'IMC
    public function calculateImc(): ?float
    {
        if ($this->getTaille() && $this->getPoids()) {
            $imc = $this->getPoids() / ($this->getTaille() * $this->getTaille());
            return round($imc, 2);
        }

        return null;
    }
}
