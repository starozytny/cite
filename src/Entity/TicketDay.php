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

    /**
     * @ORM\OneToMany(targetEntity=TicketHistory::class, mappedBy="day", orphanRemoval=true)
     */
    private $histories;

    public function __construct()
    {
        $this->setIsOpen(false);
        $this->ticketCreneaux = new ArrayCollection();
        $this->prospects = new ArrayCollection();
        $this->responsables = new ArrayCollection();
        $this->histories = new ArrayCollection();
        $this->setMax(0);
        $this->setRemaining(0);
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

    public function getDayFr(){
        $d = date_format($this->getDay(), 'l');
        switch ($d){
            case 'Monday':
                return 'Lundi';
            case 'Tuesday':
                return 'Mardi';
            case 'Wednesday':
                return 'Mercredi';
            case 'Thursday':
                return 'Jeudi';
            case 'Friday':
                return 'Vendredi';
            case 'Saturday':
                return 'Samedi';
            case 'Sunday':
                return 'Dimanche';
        }
    }

    public function getMonthFr(){
        $m = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        $d = date_format($this->getDay(), 'n');

        return $m[intval($d)];
    }

    public function getFullDateString()
    {
        return $this->getDayFr() . ' ' . date_format($this->getDay(), 'd') . ' ' . $this->getMonthFr() . ' ' . date_format($this->getDay(), 'Y');
    }

    public function getDateString()
    {
        return date_format($this->getDay(), 'd') . ' ' . $this->getMonthFr() . ' ' . date_format($this->getDay(), 'Y');
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

    /**
     * @return Collection|TicketHistory[]
     */
    public function getHistories(): Collection
    {
        return $this->histories;
    }

    public function addHistory(TicketHistory $history): self
    {
        if (!$this->histories->contains($history)) {
            $this->histories[] = $history;
            $history->setDay($this);
        }

        return $this;
    }

    public function removeHistory(TicketHistory $history): self
    {
        if ($this->histories->contains($history)) {
            $this->histories->removeElement($history);
            // set the owning side to null (unless already changed)
            if ($history->getDay() === $this) {
                $history->setDay(null);
            }
        }

        return $this;
    }
}
