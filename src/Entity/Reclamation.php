<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use App\Entity\User;
use symfony\component\Validator\Constraints as assert;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;





    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Le champ nom ne peut pas être vide.")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"L'objet est obligatoire.")]
    private ?string $obj = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Le contenu est obligatoire.")]
    private ?string $contenu = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column]
    private ?bool $Etat = null;

    #[ORM\ManyToOne(inversedBy: 'reclationID')]
    private ?User $reclamationUser = null;

 


    #[ORM\OneToMany(targetEntity: Reponse::class, mappedBy: 'reponserelation')]
    private Collection $reponses;

    public function __construct()
    {
        $this->reponses = new ArrayCollection();
    }
    






   

    public function getId(): ?int
    {
        return $this->id;
    }




    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getObj(): ?string
    {
        return $this->obj;
    }

    public function setObj(string $obj): static
    {
        $this->obj = $obj;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function isEtat(): ?bool
    {
        return $this->Etat;
    }

    public function setEtat(bool $Etat): self
    {
        $this->Etat = $Etat;

        return $this;
    }

    public function __toString()
    {
        // Customize this method based on how you want to represent the object as a string
        return (string) $this->id;
    }

    /**
     * @return Collection<int, Reponse>
     */
    public function getReponses(): Collection
    {
        return $this->reponses;
    }

    public function addReponse(Reponse $reponse): static
    {
        if (!$this->reponses->contains($reponse)) {
            $this->reponses->add($reponse);
            $reponse->setReponserelation($this);
        }

        return $this;
    }

    public function removeReponse(Reponse $reponse): static
    {
        if ($this->reponses->removeElement($reponse)) {
            // set the owning side to null (unless already changed)
            if ($reponse->getReponserelation() === $this) {
                $reponse->setReponserelation(null);
            }
        }

        return $this;
    }

    public function getReclamationUser(): ?User
    {
        return $this->reclamationUser;
    }

    public function setReclamationUser(?User $reclamationUser): static
    {
        $this->reclamationUser = $reclamationUser;

        return $this;
    }




 



}
