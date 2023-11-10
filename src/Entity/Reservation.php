<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $cin = null;

    #[ORM\Column]
    private ?String $nom_u = null;
    #[ORM\Column]
    private ?String $prenom_u = null;


    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?EventUser $event = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvent(): ?EventUser
    {
        return $this->event;
    }

    public function setEvent(?EventUser $event): static
    {
        $this->event = $event;

        return $this;
    }

    public function getCin(): ?int
    {
        return $this->cin;
    }

    public function setCin(int $cin): static
    {
        $this->cin = $cin;

        return $this;
    }

    public function getNomU(): ?string
    {
        return $this->nom_u;
    }

    public function setNomU(string $nom_u): static
    {
        $this->nom_u = $nom_u;

        return $this;
    }

    public function getPrenomU(): ?string
    {
        return $this->prenom_u;
    }

    public function setPrenomU(string $prenom_u): static
    {
        $this->prenom_u = $prenom_u;

        return $this;
    }
}
