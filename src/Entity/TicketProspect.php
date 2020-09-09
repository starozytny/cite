<?php

namespace App\Entity;

use App\Entity\Cite\CiAdherent;
use App\Repository\TicketProspectRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TicketProspectRepository::class)
 */
class TicketProspect
{
    const ST_ATTENTE = 0;
    const ST_CONFIRMED = 1;
    const ST_REGISTERED = 2;
    const ST_WAITING = 99;

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
     * @ORM\Column(type="datetime")
     */
    private $birthday;

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
     * @ORM\Column(type="string", length=10)
     */
    private $cp;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $numAdh;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createAt;

    /**
     * @ORM\ManyToOne(targetEntity=TicketResponsable::class, fetch="EAGER", inversedBy="prospects")
     * @ORM\JoinColumn(nullable=false)
     */
    private $responsable;

    /**
     * @ORM\ManyToOne(targetEntity=TicketCreneau::class, inversedBy="prospects")
     */
    private $creneau;

    /**
     * @ORM\ManyToOne(targetEntity=TicketDay::class, inversedBy="prospects")
     */
    private $day;

    private $age;

    /**
     * @ORM\OneToOne(targetEntity=CiAdherent::class, cascade={"persist", "remove"})
     */
    private $adherent;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDiff;

    public function __construct()
    {
        date_default_timezone_set('Europe/Paris');
        $this->setCreateAt(new DateTime());
        $this->setStatus(self::ST_ATTENTE);
        $this->setIsDiff(false);
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

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

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

    public function getNumAdh(): ?string
    {
        return $this->numAdh;
    }

    public function setNumAdh(?string $numAdh): self
    {
        $this->numAdh = $numAdh;

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

    public function getStatusString(){
        switch($this->getStatus()){
            case self::ST_WAITING:
                return "File attente";
                break;
            case self::ST_ATTENTE:
                return "Attente";
                break;
            case self::ST_CONFIRMED:
                return "Attente";
                break;
            case self::ST_REGISTERED:
                return "Inscrit";
                break;
        }
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

    public function getResponsable(): ?TicketResponsable
    {
        return $this->responsable;
    }

    public function setResponsable(?TicketResponsable $responsable): self
    {
        $this->responsable = $responsable;

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

    public function getAge()
    {
        $nowyear = date_format(new DateTime(), 'Y');
        $birthday = date_format($this->getBirthday(), 'Y');
        $age = intval($nowyear) - intval($birthday);

        $age = $age > 1 ? $age . ' ans' : $age . ' an';

        $this->age = $age;

        return $age;
    }

    public function getBirthdayString(){
        return date_format($this->getBirthday(), 'd/m/Y');
    }

    public function getBirthdayJavascript(){
        date_default_timezone_set('Europe/Paris');
        return date_format($this->getBirthday(), 'F d, Y 00:00:00');
    }

    public function getAdresseString(){
        return $this->getAdr() . ', ' . $this->getCp() . " " . $this->getCity();
    }

    public function getAdherent(): ?CiAdherent
    {
        return $this->adherent;
    }

    public function setAdherent(?CiAdherent $adherent): self
    {
        $this->adherent = $adherent;

        return $this;
    }

    public function getIsDiff(): ?bool
    {
        return $this->isDiff;
    }

    public function setIsDiff(bool $isDiff): self
    {
        $this->isDiff = $isDiff;

        return $this;
    }
}
