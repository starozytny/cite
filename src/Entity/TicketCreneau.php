<?php

namespace App\Entity;

use App\Repository\TicketCreneauRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TicketCreneauRepository::class)
 */
class TicketCreneau
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $horaire;

    /**
     * @ORM\Column(type="integer")
     */
    private $max;

    /**
     * @ORM\Column(type="integer")
     */
    private $remaining;

    /**
     * @ORM\ManyToOne(targetEntity=TicketDay::class, inversedBy="ticketCreneaux")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ticketDay;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHoraire(): ?string
    {
        return $this->horaire;
    }

    public function setHoraire(string $horaire): self
    {
        $this->horaire = $horaire;

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

    public function getTicketDay(): ?TicketDay
    {
        return $this->ticketDay;
    }

    public function setTicketDay(?TicketDay $ticketDay): self
    {
        $this->ticketDay = $ticketDay;

        return $this;
    }
}
