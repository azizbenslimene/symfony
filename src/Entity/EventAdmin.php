<?php

namespace App\Entity;

use App\Repository\EventAdminRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventAdminRepository::class)]
class EventAdmin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_a = null;

    #[ORM\Column(length: 255)]
    private ?string $date_a = null;

    #[ORM\Column(length: 255)]
    private ?string $lieu_a = null;

    #[ORM\Column(length: 255)]
    private ?string $description_a = null;

    #[ORM\Column(length: 255)]
    private ?string $image_a = null;

    #[ORM\Column]
    private ?int $prix_a = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomA(): ?string
    {
        return $this->nom_a;
    }

    public function setNomA(string $nom_a): static
    {
        $this->nom_a = $nom_a;

        return $this;
    }

    public function getDateA(): ?string
    {
        return $this->date_a;
    }

    public function setDateA(string $date_a): static
    {
        $this->date_a = $date_a;

        return $this;
    }

    public function getLieuA(): ?string
    {
        return $this->lieu_a;
    }

    public function setLieuA(string $lieu_a): static
    {
        $this->lieu_a = $lieu_a;

        return $this;
    }

    public function getDescriptionA(): ?string
    {
        return $this->description_a;
    }

    public function setDescriptionA(string $description_a): static
    {
        $this->description_a = $description_a;

        return $this;
    }

    public function getImageA(): ?string
    {
        return $this->image_a;
    }

    public function setImageA(string $image_a): static
    {
        $this->image_a = $image_a;

        return $this;
    }

    public function getPrixA(): ?int
    {
        return $this->prix_a;
    }

    public function setPrixA(int $prix_a): static
    {
        $this->prix_a = $prix_a;

        return $this;
    }
}
