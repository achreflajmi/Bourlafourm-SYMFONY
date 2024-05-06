<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;


use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_rese_event = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le nombre de place est obligatoire.")]
    #[Assert\GreaterThan(value: 0, message: "Le nombre de place doit être supérieure à zéro")]
    private ?int $nbr_place_reserv = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Email est obligatoire.")]
    #[Assert\Email(message: "L'adresse email '{{ value }}' n'est pas valide.")]
    private ?string $Email = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(name: 'id_reser_event', referencedColumnName: 'id_event')]
    private ?Evenement $id_reser_event = null;

    public function getId(): ?int
    {
        return $this->id;
    }


    

    public function getNomReseEvent(): ?string
    {
        return $this->nom_rese_event;
    }
    public function getnom_rese_event(): ?string
    {
        return $this->nom_rese_event;
    }

    public function setNomReseEvent(string $nom_rese_event): static
    {
        $this->nom_rese_event = $nom_rese_event;

        return $this;
    }
    

    public function getNbrPlaceReserv(): ?int
    {
        return $this->nbr_place_reserv;
    }

    public function setNbrPlaceReserv(int $nbr_place_reserv): static
    {
        $this->nbr_place_reserv = $nbr_place_reserv;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(string $Email): static
    {
        $this->Email = $Email;

        return $this;
    }

    public function getIdReserEvent(): ?Evenement
    {
        return $this->id_reser_event;
    }

    public function setIdReserEvent(?Evenement $id_reser_event): static
    {
        $this->id_reser_event = $id_reser_event;

        return $this;
    }
}
