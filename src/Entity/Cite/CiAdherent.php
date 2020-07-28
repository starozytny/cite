<?php

namespace App\Entity\Cite;

use App\Entity\TicketProspect;
use App\Repository\Cite\CiAdherentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use \App\Entity\Cite\CiPersonne;

/**
 * @ORM\Entity(repositoryClass=CiAdherentRepository::class)
 * @ORM\Table(name="ci_adherent")
 */
class CiAdherent
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
    private $oldId;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $numAdh;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAncien;

    /**
     * @ORM\ManyToOne(targetEntity=CiPersonne::class, inversedBy="adherents")
     */
    private $personne;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $civility;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="datetime", nullable=true)
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adr;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $cp;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @ORM\OneToMany(targetEntity=TicketProspect::class, mappedBy="adherent")
     */
    private $prospects;

    public function __construct()
    {
        $this->prospects = new ArrayCollection();
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

    public function setCivility(string $civility): self
    {
        $this->civility = $civility;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): self
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

    public function setAdr(?string $adr): self
    {
        $this->adr = $adr;

        return $this;
    }

    public function getCp(): ?string
    {
        return $this->cp;
    }

    public function setCp(?string $cp): self
    {
        $this->cp = $cp;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getNumAdh(): ?string
    {
        return $this->numAdh;
    }

    public function setNumAdh(string $numAdh): self
    {
        $this->numAdh = $numAdh;

        return $this;
    }

    public function getIsAncien(): ?bool
    {
        return $this->isAncien;
    }

    public function setIsAncien(bool $isAncien): self
    {
        $this->isAncien = $isAncien;

        return $this;
    }

    public function getOldId(): ?int
    {
        return $this->oldId;
    }

    public function setOldId(int $oldId): self
    {
        $this->oldId = $oldId;

        return $this;
    }

    public function getPersonne(): ?CiPersonne
    {
        return $this->personne;
    }

    public function setPersonne(?CiPersonne $personne): self
    {
        $this->personne = $personne;

        return $this;
    }

    public function getBirthdayJavascript(){
        date_default_timezone_set('Europe/Paris');
        return $this->getBirthday() != null ? date_format($this->getBirthday(), 'F d, Y 00:00:00') : null;
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
            $prospect->setAdherent($this);
        }

        return $this;
    }

    public function removeProspect(TicketProspect $prospect): self
    {
        if ($this->prospects->contains($prospect)) {
            $this->prospects->removeElement($prospect);
            // set the owning side to null (unless already changed)
            if ($prospect->getAdherent() === $this) {
                $prospect->setAdherent(null);
            }
        }

        return $this;
    }
}
