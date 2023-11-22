<?php

namespace App\Entity;

use App\Repository\EventUserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: EventUserRepository::class)]


#[Assert\Callback(callback: 'validateDate')]
class EventUser
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
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"La date ne doit pas être vide.")]
    
    private ?string $date = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Le lieu ne doit pas être vide.")]
    private ?string $lieu = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"La description ne doit pas être vide.")]
    private ?string $description = null;


    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"Le prix ne doit pas être vide.")]
    #[Assert\Type(type:"integer", message:"Le prix doit être un entier.")]
    private ?int $prix = null;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Reservation::class )]
    #[ORM\JoinColumn(name:"event_id", referencedColumnName:"id", onDelete:"CASCADE")]
    private Collection $reservations;

    #[ORM\ManyToMany(targetEntity: EventAdmin::class, mappedBy: 'id_ev')]
    private Collection $eventAdmins;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->eventAdmins = new ArrayCollection();
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

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * @return Collection|Reservation[]
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setEvent($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getEvent() === $this) {
                $reservation->setEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EventAdmin>
     */
    public function getEventAdmins(): Collection
    {
        return $this->eventAdmins;
    }

    public function addEventAdmin(EventAdmin $eventAdmin): static
    {
        if (!$this->eventAdmins->contains($eventAdmin)) {
            $this->eventAdmins->add($eventAdmin);
            $eventAdmin->addIdEv($this);
        }

        return $this;
    }

    public function removeEventAdmin(EventAdmin $eventAdmin): static
    {
        if ($this->eventAdmins->removeElement($eventAdmin)) {
            $eventAdmin->removeIdEv($this);
        }

        return $this;
    }


    public function validateDate(ExecutionContextInterface $context): void
    {
        // Valider le format de date
        $format = 'd-MM-yyyy'; // Format attendu
        $dateString = $this->date;

        $date = \DateTime::createFromFormat($format, $dateString);

        if (!$date || $date->format($format) !== $dateString) {
            $context->buildViolation('Le format de date n\'est pas valide.')
                ->atPath('date')
                ->addViolation();
        }
    }
}
