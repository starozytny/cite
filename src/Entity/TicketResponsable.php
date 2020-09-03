<?php

namespace App\Entity;

use App\Repository\TicketResponsableRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TicketResponsableRepository::class)
 */
class TicketResponsable
{
    const ST_TMP = 0;
    const ST_CONFIRMED = 1;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $civility;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $phoneDomicile;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $phoneMobile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adr;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $complement;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $cp;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\OneToMany(targetEntity=TicketProspect::class, mappedBy="responsable", fetch="EAGER", orphanRemoval=true)
     */
    private $prospects;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isWaiting;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ticket;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=TicketCreneau::class, inversedBy="responsables")
     */
    private $creneau;

    /**
     * @ORM\ManyToOne(targetEntity=TicketDay::class, inversedBy="responsables")
     */
    private $day;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isMobile;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $browser;

    public function __construct()
    {
        $this->prospects = new ArrayCollection();
        $this->setCreateAt(new DateTime());
        $this->setIsWaiting(false);
        $this->setStatus(self::ST_TMP);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getCivility(): ?string
    {
        return $this->civility;
    }

    public function setCivility(?string $civility): self
    {
        $this->civility = $civility;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneDomicile(): ?string
    {
        return $this->phoneDomicile;
    }

    public function setPhoneDomicile(?string $phoneDomicile): self
    {
        $this->phoneDomicile = $phoneDomicile;

        return $this;
    }

    public function getPhoneMobile(): ?string
    {
        return $this->phoneMobile;
    }

    public function setPhoneMobile(?string $phoneMobile): self
    {
        $this->phoneMobile = $phoneMobile;

        return $this;
    }

    public function getAdr(): ?string
    {
        return $this->adr;
    }

    public function setAdr(string $adr): self
    {
        $this->adr = $adr;

        return $this;
    }

    public function getComplement(): ?string
    {
        return $this->complement;
    }

    public function setComplement(?string $complement): self
    {
        $this->complement = $complement;

        return $this;
    }

    public function getCp(): ?string
    {
        return $this->cp;
    }

    public function setCp(string $cp): self
    {
        $this->cp = $cp;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

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
            $prospect->setResponsable($this);
        }

        return $this;
    }

    public function removeProspect(TicketProspect $prospect): self
    {
        if ($this->prospects->contains($prospect)) {
            $this->prospects->removeElement($prospect);
            // set the owning side to null (unless already changed)
            if ($prospect->getResponsable() === $this) {
                $prospect->setResponsable(null);
            }
        }

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeInterface $createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getIsWaiting(): ?bool
    {
        return $this->isWaiting;
    }

    public function setIsWaiting(bool $isWaiting): self
    {
        $this->isWaiting = $isWaiting;

        return $this;
    }

    public function getTicket(): ?string
    {
        return $this->ticket;
    }

    public function setTicket(?string $ticket): self
    {
        $this->ticket = $ticket;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreneau(): ?TicketCreneau
    {
        return $this->creneau;
    }

    public function setCreneau(?TicketCreneau $creneau): self
    {
        $this->creneau = $creneau;

        return $this;
    }

    public function getDay(): ?TicketDay
    {
        return $this->day;
    }

    public function setDay(?TicketDay $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getCreateAtString(){
        date_default_timezone_set('Europe/Paris');
        return date_format($this->getCreateAt(), 'd/m/Y \à H\hi');
    }

    public function getAdresseString(){
        return $this->getAdr() . ', ' . $this->getCp() . " " . $this->getCity();
    }

    public function getIsMobile(): ?bool
    {
        return $this->isMobile;
    }

    public function setIsMobile(bool $isMobile): self
    {
        $this->isMobile = $isMobile;

        return $this;
    }

    public function getBrowser(): ?string
    {
        return $this->browser;
    }

    public function setBrowser(?string $browser): self
    {
        $this->browser = $browser;

        return $this;
    }
}
