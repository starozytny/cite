<?php

namespace App\Entity;

use App\Repository\TicketDayRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TicketDayRepository::class)
 */
class TicketDay
{
    const TYPE_ANCIEN = 0;
    const TYPE_NOUVEAU = 1;
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @ORM\Column(type="datetime")
     */
    private $day;

    /**
     * @ORM\Column(type="integer")
     */
    private $max;

    /**
     * @ORM\Column(type="integer")
     */
    private $remaining;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isOpen;

    /**
     * @ORM\OneToMany(targetEntity=TicketCreneau::class, mappedBy="ticketDay", orphanRemoval=true)
     */
    private $ticketCreneaux;

    /**
     * @ORM\OneToMany(targetEntity=TicketProspect::class, mappedBy="day")
     */
    private $prospects;

    /**
     * @ORM\OneToMany(targetEntity=TicketResponsable::class, mappedBy="day")
     */
    private $responsables;

    public function __construct()
    {
        $this->setIsOpen(false);
        $this->ticketCreneaux = new ArrayCollection();
        $this->prospects = new ArrayCollection();
        $this->responsables = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDay(): ?\DateTimeInterface
    {
        return $this->day;
    }

    public function setDay(\DateTimeInterface $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getMax(): ?int
    {
        return $this->max;
    }

    public function setMax(int $max): self
    {
        $this->max = $max;

        return $this;
    }

    public function getRemaining(): ?int
    {
        return $this->remaining;
    }

    public function setRemaining(int $remaining): self
    {
        $this->remaining = $remaining;

        return $this;
    }

    public function getTypeString()
    {
        return $this->getType() == self::TYPE_ANCIEN ? "anciens" : "nouveaux";
    }

    public function getIsOpen(): ?bool
    {
        return $this->isOpen;
    }

    public function setIsOpen(bool $isOpen): self
    {
        $this->isOpen = $isOpen;

        return $this;
    }

    /**
     * @return Collection|TicketCreneau[]
     */
    public function getTicketCreneaux(): Collection
    {
        return $this->ticketCreneaux;
    }

    public function addTicketCreneaux(TicketCreneau $ticketCreneaux): self
    {
        if (!$this->ticketCreneaux->contains($ticketCreneaux)) {
            $this->ticketCreneaux[] = $ticketCreneaux;
            $ticketCreneaux->setTicketDay($this);
        }

        return $this;
    }

    public function removeTicketCreneaux(TicketCreneau $ticketCreneaux): self
    {
        if ($this->ticketCreneaux->contains($ticketCreneaux)) {
            $this->ticketCreneaux->removeElement($ticketCreneaux);
            // set the owning side to null (unless already changed)
            if ($ticketCreneaux->getTicketDay() === $this) {
                $ticketCreneaux->setTicketDay(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TicketProspect[]
     */
    public function getProspects(): Collection
    {
        return $this->prospects;
    }

    public function addProspect(TicketProspect $prospect): self
    {
        if (!$this->prospects->contains($prospect)) {
            $this->prospects[] = $prospect;
            $prospect->setDay($this);
        }

        return $this;
    }

    public function removeProspect(TicketProspect $prospect): self
    {
        if ($this->prospects->contains($prospect)) {
            $this->prospects->removeElement($prospect);
            // set the owning side to null (unless already changed)
            if ($prospect->getDay() === $this) {
                $prospect->setDay(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TicketResponsable[]
     */
    public function getResponsables(): Collection
    {
        return $this->responsables;
    }

    public function addResponsable(TicketResponsable $responsable): self
    {
        if (!$this->responsables->contains($responsable)) {
            $this->responsables[] = $responsable;
            $responsable->setDay($this);
        }

        return $this;
    }

    public function removeResponsable(TicketResponsable $responsable): self
    {
        if ($this->responsables->contains($responsable)) {
            $this->responsables->removeElement($responsable);
            // set the owning side to null (unless already changed)
            if ($responsable->getDay() === $this) {
                $responsable->setDay(null);
            }
        }

        return $this;
    }
}
