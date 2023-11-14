<?php

namespace App\Entity;

use App\Repository\EventAdminRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EventAdminRepository::class)]
class EventAdmin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Le nom ne doit pas être vide.")]
    #[Assert\Type(type:"string", message:"Le nom doit être une chaîne de caractères.")]
    #[Assert\Regex(
        pattern:"/^[^\d]+$/",
        message:"Le nom ne doit pas contenir de chiffres.")]
    private ?string $nom_a = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"La date ne doit pas être vide.")]
    private ?string $date_a = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Le lieu ne doit pas être vide.")]
    private ?string $lieu_a = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"La description ne doit pas être vide.")]
    private ?string $description_a = null;

    #[ORM\Column(length: 255)]
    private ?string $image_a = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"Le prix ne doit pas être vide.")]
    #[Assert\Type(type:"integer", message:"Le prix doit être un entier.")]
    private ?int $prix_a = null;

    #[ORM\ManyToMany(targetEntity: EventUser::class, inversedBy: 'eventAdmins')]
    private Collection $id_ev;

    public function __construct()
    {
        $this->id_ev = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, EventUser>
     */
    public function getIdEv(): Collection
    {
        return $this->id_ev;
    }

    public function addIdEv(EventUser $idEv): static
    {
        if (!$this->id_ev->contains($idEv)) {
            $this->id_ev->add($idEv);
        }

        return $this;
    }

    public function removeIdEv(EventUser $idEv): static
    {
        $this->id_ev->removeElement($idEv);

        return $this;
    }
}
