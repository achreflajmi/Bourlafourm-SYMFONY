<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Le nom est obligatoire.")]
    private ?string $Nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\Email(message : "L'email '{{ value }}' n'est pas valide.")]
    private ?string $Email = null;


    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"La description est obligatoire")]
    private ?string $Description = null;

    #[ORM\ManyToOne(inversedBy: 'reponses')]
    private ?Reclamation $reponserelation = null;

   

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): self
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(string $Email): self
    {
        $this->Email = $Email;

        return $this;
    }

  

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    public function getReponserelation(): ?Reclamation
    {
        return $this->reponserelation;
    }

    public function setReponserelation(?Reclamation $reponserelation): static
    {
        $this->reponserelation = $reponserelation;

        return $this;
    }
}
