<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;


#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    
    private int $idEvent;

    
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de l'événement est obligatoire.")]
    #[Assert\Regex(
            pattern:"/^[a-zA-Z]+$/",
             message:"Le nom doit contenir que des lettres.")]
    private ?string $NomEvent = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "le type est obligatoire.")]
    #[Assert\Regex(
        pattern:"/^[a-zA-Z]+$/",
         message:"Le type doit contenir que des lettres.")]
    private ?string$Type=null;



    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "l'organisateur est obligatoire.")]
    #[Assert\Regex(
        pattern:"/^[a-zA-Z]+$/",
         message:"L'organisateur doit contenir que des lettres.")]
    private ?string$organisateur=null;



    private DateTime $today;

    public function __construct()
    {
        $this->today = new DateTime();
        $this->reservations = new ArrayCollection(); 
    }

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "Date debut est obligatoire.")]
    #[Assert\GreaterThan(propertyPath: "today", message: "La date de debut doit être supérieure à la date d'aujourd'hui")]
    private ?\DateTime $Date_deb;

   

    public function gettoday(): \DateTime
    {
        return new \DateTime(); // Renvoie la date d'aujourd'hui
    }




    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "date fin est obligatoire.")]
    #[Assert\GreaterThan(propertyPath: "Date_deb", message :"La date de fin doit être postérieure à la date de début")]
    private ?\DateTime $Date_fin;



    #[ORM\Column]
    #[Assert\NotBlank(message: "La capacité est obligatoire.")]
    #[Assert\GreaterThan(value: 0, message: "La capacité doit être supérieure à zéro")]
    private ?int $Capacite=null ;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'image est obligatoire.")]

    private  $Image ;

    #[ORM\Column(length: 255)]
    private $Path ;


    #[ORM\Column(nullable:true)] 
      private $nb_place_res ;

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'id_reser_event')]
    private Collection $reservations;

   

    public function getIdEvent(): ?int
    {
        return $this->idEvent;
    }

    public function setIdEvent(int $idEvent): static
    {
        $this->idEvent = $idEvent;

        return $this;
    }

    public function getNomEvent(): ?string
    {
        return $this->NomEvent;
    }

    public function setNomEvent(?string $NomEvent): static
    {
        $this->NomEvent = $NomEvent;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->Type;
    }

    public function setType(string $Type): static
    {
        $this->Type = $Type;

        return $this;
    }

    public function getOrganisateur(): ?string
    {
        return $this->organisateur;
    }

    public function setOrganisateur(string $organisateur): static
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    public function getDate_deb(): ?\DateTime
    {
        return $this->Date_deb;
    }

    public function getDateDeb(): ?\DateTimeInterface
    {
        return $this->Date_deb;
    }



    public function setDateDeb(?\DateTime $date_deb): static
    {
        $this->Date_deb = $date_deb;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->Date_fin;
    }



    public function getDate_fin(): ?\DateTime
    {
        return $this->Date_fin;
    }

    public function setDateFin(?\DateTime $Date_fin): static
    {
        $this->Date_fin = $Date_fin;

        return $this;
    }

    public function getCapacite(): ?int
    {
        return $this->Capacite;
    }

    public function setCapacite(int $Capacite): static
    {
        $this->Capacite = $Capacite;

        return $this;
    }

    
    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->Image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image): void
    {
        $this->Image = $image;
    }
    public function getPath(): ?string
    {
        return $this->Path;
    }

    public function setPath(string $Path): static
    {
        $this->Path = $Path;

        return $this;
    }

    public function getNbPlaceRes(): ?int
    {
        return $this->nb_place_res;
    }

    public function setNbPlaceRes(int $nb_place_res): static
    {
        $this->nb_place_res = $nb_place_res;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setIdReserEvent($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getIdReserEvent() === $this) {
                $reservation->setIdReserEvent(null);
            }
        }

        return $this;
    }
    
}
