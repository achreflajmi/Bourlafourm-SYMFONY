<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;



#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
// #[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
    class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user', 'posts:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['user', 'posts:read'])]
    
    private ?string $email = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"L'objet est obligatoire.")]
    #[Groups(['user', 'posts:read'])]
    private string $role;

    /**
     * @var string The hashed motpass
     */
    #[ORM\Column(name: "motpass")]
  
    #[Groups(['user', 'posts:read'])]
    private ?string $motpass = null;


    #[ORM\Column(length: 255)]

    #[Groups(['user', 'posts:read'])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user', 'posts:read'])]

    private ?string $image = null;

    #[ORM\Column(name: "reset_code", nullable: true)]
    private ?string $resetCode = null;

    /**
     */
    private ?string $confirmPassword = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user', 'posts:read'])]

    private ?string $poinds_sportif = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user', 'posts:read'])]
  
    private ?string $taille_sportif = null;

    #[ORM\Column]
    #[Groups(['user', 'posts:read'])]
    private ?bool $disponibilite = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user', 'posts:read'])]

    private ?string $prenom = null;
    //  #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    
    private ?\DateTimeInterface $dateN = null;
    

     #[ORM\Column]
     #[Groups(['user', 'posts:read'])]
     private ?bool $isReported = false;

#[ORM\OneToMany(targetEntity: Reclamation::class, mappedBy: 'reclamationUser', cascade: ["remove"])]
     private Collection $reclationID;

 

    public function __construct() {
        $this->dateN = new \DateTime(); // Ou une autre logique pour définir une date initiale
        $this->reclationID = new ArrayCollection();
    }


    
 

    public function getIsReported(): bool
    {
        return $this->isReported;
    }

    public function setIsReported(bool $isReported): self
    {
        $this->isReported = $isReported;

        return $this;
    }




    public function getId(): ?int
    {
        return $this->id;
    }
    public function getUsername(): string
    {
        return $this->email;
    }

    public function getSalt()
    {
        return null;
    }

 
    
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $role = $this->role;
        $role = explode(',', $role);

        return array_unique($role);
    }



    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->motpass;
    }

    public function setPassword(string $motpass): self
    {
        $this->motpass = $motpass;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
    }

    public function getName(): ?string
    {
        return $this->nom;
    }

    public function setName(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getResetCode(): ?string
    {
        return $this->resetCode;
    }

    public function setResetCode(?string $resetCode): self
    {
        $this->resetCode = $resetCode;

        return $this;
    }

    

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(?string $confirmPassword): self
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }

    public function setRoles(string $roles): self
{
    $this->role = $roles;

    return $this;
}

    public function getPoindsSportif(): ?string
    {
        return $this->poinds_sportif;
    }

    public function setPoindsSportif(string $poinds_sportif): self
    {
        $this->poinds_sportif = $poinds_sportif;

        return $this;
    }

    public function getTailleSportif(): ?string
    {
        return $this->taille_sportif;
    }

    public function setTailleSportif(string $taille_sportif): self
    {
        $this->taille_sportif = $taille_sportif;

        return $this;
    }

    public function isDisponibilite(): ?bool
    {
        return $this->disponibilite;
    }

    public function setDisponibilite(bool $disponibilite): static
    {
        $this->disponibilite = $disponibilite;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateN(): ?\DateTimeInterface
    {
        return $this->dateN;
    }

    public function setDateN(\DateTimeInterface $dateN): static
    {
        $this->dateN = $dateN;

        return $this;
    }

    /**
     * @return Collection<int, Reclamation>
     */
    public function getReclationID(): Collection
    {
        return $this->reclationID;
    }

    public function addReclationID(Reclamation $reclationID): static
    {
        if (!$this->reclationID->contains($reclationID)) {
            $this->reclationID->add($reclationID);
            $reclationID->setReclamationUser($this);
        }

        return $this;
    }

    public function removeReclationID(Reclamation $reclationID): static
    {
        if ($this->reclationID->removeElement($reclationID)) {
            // set the owning side to null (unless already changed)
            if ($reclationID->getReclamationUser() === $this) {
                $reclationID->setReclamationUser(null);
            }
        }

        return $this;
    }

  






}
