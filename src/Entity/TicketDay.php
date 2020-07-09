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

    public function __construct()
    {
        $this->setIsOpen(false);
        $this->ticketCreneaux = new ArrayCollection();
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
}