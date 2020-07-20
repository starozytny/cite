<?php

namespace App\Entity;

use App\Repository\TicketHistoryRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TicketHistoryRepository::class)
 */
class TicketHistory
{
    const STEP_START = 0;
    const STEP_FAMILLE = 1;
    const STEP_RESP = 2;
    const STEP_TICKET = 3;

    const STATE_TMP = 0;
    const STATE_CONFIRMED = 1;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $civility;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="integer")
     */
    private $step;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createAt;

    /**
     * @ORM\Column(type="integer")
     */
    private $famille;

    /**
     * @ORM\ManyToOne(targetEntity=TicketDay::class, inversedBy="histories")
     * @ORM\JoinColumn(nullable=false)
     */
    private $day;

    /**
     * @ORM\ManyToOne(targetEntity=TicketCreneau::class, inversedBy="histories")
     * @ORM\JoinColumn(nullable=false)
     */
    private $creneau;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    public function __construct()
    {
        $this->setCreateAt(new DateTime());
        $this->setStep(self::STEP_START);
        $this->setStatus(self::STATE_TMP);
        $this->setFamille(0);
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getStep(): ?int
    {
        return $this->step;
    }

    public function setStep(int $step): self
    {
        $this->step = $step;

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

    public function getFamille(): ?int
    {
        return $this->famille;
    }

    public function setFamille(int $famille): self
    {
        $this->famille = $famille;

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

    public function getCreneau(): ?TicketCreneau
    {
        return $this->creneau;
    }

    public function setCreneau(?TicketCreneau $creneau): self
    {
        $this->creneau = $creneau;

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
}
