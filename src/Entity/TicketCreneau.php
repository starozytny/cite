<?php

namespace App\Entity;

use App\Repository\TicketCreneauRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @ORM\Column(type="time")
     */
    private $horaire;

    /**
     * @ORM\OneToMany(targetEntity=TicketProspect::class, mappedBy="creneau")
     */
    private $prospects;

    /**
     * @ORM\OneToMany(targetEntity=TicketResponsable::class, mappedBy="creneau")
     */
    private $responsables;

    public function __construct()
    {
        $this->prospects = new ArrayCollection();
        $this->responsables = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getHoraire(): ?\DateTimeInterface
    {
        return $this->horaire;
    }

    public function setHoraire(\DateTimeInterface $horaire): self
    {
        $this->horaire = $horaire;

        return $this;
    }

    public function getHoraireString(){
        return date_format($this->getHoraire(), 'H\hi');
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
            $prospect->setCreneau($this);
        }

        return $this;
    }

    public function removeProspect(TicketProspect $prospect): self
    {
        if ($this->prospects->contains($prospect)) {
            $this->prospects->removeElement($prospect);
            // set the owning side to null (unless already changed)
            if ($prospect->getCreneau() === $this) {
                $prospect->setCreneau(null);
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
            $responsable->setCreneau($this);
        }

        return $this;
    }

    public function removeResponsable(TicketResponsable $responsable): self
    {
        if ($this->responsables->contains($responsable)) {
            $this->responsables->removeElement($responsable);
            // set the owning side to null (unless already changed)
            if ($responsable->getCreneau() === $this) {
                $responsable->setCreneau(null);
            }
        }

        return $this;
    }
}
