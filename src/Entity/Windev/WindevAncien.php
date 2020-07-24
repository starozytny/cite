<?php

namespace App\Entity\Windev;

use Doctrine\ORM\Mapping as ORM;

/**
 * WindevAncien
 *
 * @ORM\Table(name="windev_ancien")
 * @ORM\Entity(repositoryClass="App\Repository\Windev\WindevAncienRepository")
 */
class WindevAncien
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="PECLEUNIK", type="decimal", precision=38, scale=0, nullable=false)
     */
    private $pecleunik;

    /**
     * @var string
     *
     * @ORM\Column(name="NUM_FICHE", type="decimal", precision=38, scale=0, nullable=false)
     */
    private $numFiche;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NUM_FAMILLE", type="decimal", precision=38, scale=0, nullable=true)
     */
    private $numFamille;

    /**
     * @var string
     *
     * @ORM\Column(name="NOM", type="string", length=27, nullable=false)
     */
    private $nom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="PRENOM", type="string", length=18, nullable=true)
     */
    private $prenom;

    /**
     * @var bool
     *
     * @ORM\Column(name="TICLEUNIK", type="boolean", nullable=false)
     */
    private $ticleunik;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NAISSANCE", type="decimal", precision=38, scale=0, nullable=true)
     */
    private $naissance;

    /**
     * @var string
     *
     * @ORM\Column(name="SEXE", type="decimal", precision=38, scale=0, nullable=false)
     */
    private $sexe;

    /**
     * @var string
     *
     * @ORM\Column(name="CARTEADHERENT", type="decimal", precision=38, scale=0, nullable=false)
     */
    private $carteadherent;

    /**
     * @var bool
     *
     * @ORM\Column(name="TYCLEUNIK", type="boolean", nullable=false)
     */
    private $tycleunik;

    /**
     * @var string|null
     *
     * @ORM\Column(name="INSCRIPTION", type="decimal", precision=38, scale=0, nullable=true)
     */
    private $inscription;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ADHESION", type="string", length=4, nullable=true)
     */
    private $adhesion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="RENOUVELLEMENT", type="string", length=4, nullable=true)
     */
    private $renouvellement;

    /**
     * @var string|null
     *
     * @ORM\Column(name="SORTIE", type="decimal", precision=38, scale=0, nullable=true)
     */
    private $sortie;

    /**
     * @var string
     *
     * @ORM\Column(name="NOCOMPTA", type="decimal", precision=38, scale=0, nullable=false)
     */
    private $nocompta;

    /**
     * @var string
     *
     * @ORM\Column(name="CECLEUNIK", type="decimal", precision=38, scale=0, nullable=false)
     */
    private $cecleunik;

    /**
     * @var string|null
     *
     * @ORM\Column(name="COMMENT", type="string", length=545, nullable=true)
     */
    private $comment;

    /**
     * @var string
     *
     * @ORM\Column(name="NOTARIF", type="decimal", precision=38, scale=0, nullable=false)
     */
    private $notarif;

    /**
     * @var string|null
     *
     * @ORM\Column(name="DATECREATION", type="decimal", precision=38, scale=0, nullable=true)
     */
    private $datecreation;

    /**
     * @var string
     *
     * @ORM\Column(name="DATEMAJ", type="decimal", precision=38, scale=0, nullable=false)
     */
    private $datemaj;

    /**
     * @var string
     *
     * @ORM\Column(name="NORAPPEL", type="decimal", precision=38, scale=0, nullable=false)
     */
    private $norappel;

    /**
     * @var bool
     *
     * @ORM\Column(name="LIENPROFESSEUR", type="boolean", nullable=false)
     */
    private $lienprofesseur;

    /**
     * @var bool
     *
     * @ORM\Column(name="DISPSOLFEGE", type="boolean", nullable=false)
     */
    private $dispsolfege;

    /**
     * @var string
     *
     * @ORM\Column(name="MTRAPPEL", type="decimal", precision=38, scale=2, nullable=false)
     */
    private $mtrappel;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TELEPHONE1", type="string", length=18, nullable=true)
     */
    private $telephone1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="INFO_TEL1", type="string", length=22, nullable=true)
     */
    private $infoTel1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TELEPHONE2", type="string", length=14, nullable=true)
     */
    private $telephone2;

    /**
     * @var string|null
     *
     * @ORM\Column(name="INFO_TEL2", type="string", length=8, nullable=true)
     */
    private $infoTel2;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ADRESSE_ADH", type="string", length=84, nullable=true)
     */
    private $adresseAdh;

    /**
     * @var string|null
     *
     * @ORM\Column(name="EMAIL_ADH", type="string", length=29, nullable=true)
     */
    private $emailAdh;

    /**
     * @var bool
     *
     * @ORM\Column(name="AssoPartenaire", type="boolean", nullable=false)
     */
    private $assopartenaire;

    /**
     * @var bool
     *
     * @ORM\Column(name="CNR_CRR", type="boolean", nullable=false)
     */
    private $cnrCrr;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPecleunik(): ?string
    {
        return $this->pecleunik;
    }

    public function setPecleunik(string $pecleunik): self
    {
        $this->pecleunik = $pecleunik;

        return $this;
    }

    public function getNumFiche(): ?string
    {
        return $this->numFiche;
    }

    public function setNumFiche(string $numFiche): self
    {
        $this->numFiche = $numFiche;

        return $this;
    }

    public function getNumFamille(): ?string
    {
        return $this->numFamille;
    }

    public function setNumFamille(?string $numFamille): self
    {
        $this->numFamille = $numFamille;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTicleunik(): ?bool
    {
        return $this->ticleunik;
    }

    public function setTicleunik(bool $ticleunik): self
    {
        $this->ticleunik = $ticleunik;

        return $this;
    }

    public function getNaissance(): ?string
    {
        return $this->naissance;
    }

    public function setNaissance(?string $naissance): self
    {
        $this->naissance = $naissance;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getCarteadherent(): ?string
    {
        return $this->carteadherent;
    }

    public function setCarteadherent(string $carteadherent): self
    {
        $this->carteadherent = $carteadherent;

        return $this;
    }

    public function getTycleunik(): ?bool
    {
        return $this->tycleunik;
    }

    public function setTycleunik(bool $tycleunik): self
    {
        $this->tycleunik = $tycleunik;

        return $this;
    }

    public function getInscription(): ?string
    {
        return $this->inscription;
    }

    public function setInscription(?string $inscription): self
    {
        $this->inscription = $inscription;

        return $this;
    }

    public function getAdhesion(): ?string
    {
        return $this->adhesion;
    }

    public function setAdhesion(?string $adhesion): self
    {
        $this->adhesion = $adhesion;

        return $this;
    }

    public function getRenouvellement(): ?string
    {
        return $this->renouvellement;
    }

    public function setRenouvellement(?string $renouvellement): self
    {
        $this->renouvellement = $renouvellement;

        return $this;
    }

    public function getSortie(): ?string
    {
        return $this->sortie;
    }

    public function setSortie(?string $sortie): self
    {
        $this->sortie = $sortie;

        return $this;
    }

    public function getNocompta(): ?string
    {
        return $this->nocompta;
    }

    public function setNocompta(string $nocompta): self
    {
        $this->nocompta = $nocompta;

        return $this;
    }

    public function getCecleunik(): ?string
    {
        return $this->cecleunik;
    }

    public function setCecleunik(string $cecleunik): self
    {
        $this->cecleunik = $cecleunik;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getNotarif(): ?string
    {
        return $this->notarif;
    }

    public function setNotarif(string $notarif): self
    {
        $this->notarif = $notarif;

        return $this;
    }

    public function getDatecreation(): ?string
    {
        return $this->datecreation;
    }

    public function setDatecreation(?string $datecreation): self
    {
        $this->datecreation = $datecreation;

        return $this;
    }

    public function getDatemaj(): ?string
    {
        return $this->datemaj;
    }

    public function setDatemaj(string $datemaj): self
    {
        $this->datemaj = $datemaj;

        return $this;
    }

    public function getNorappel(): ?string
    {
        return $this->norappel;
    }

    public function setNorappel(string $norappel): self
    {
        $this->norappel = $norappel;

        return $this;
    }

    public function getLienprofesseur(): ?bool
    {
        return $this->lienprofesseur;
    }

    public function setLienprofesseur(bool $lienprofesseur): self
    {
        $this->lienprofesseur = $lienprofesseur;

        return $this;
    }

    public function getDispsolfege(): ?bool
    {
        return $this->dispsolfege;
    }

    public function setDispsolfege(bool $dispsolfege): self
    {
        $this->dispsolfege = $dispsolfege;

        return $this;
    }

    public function getMtrappel(): ?string
    {
        return $this->mtrappel;
    }

    public function setMtrappel(string $mtrappel): self
    {
        $this->mtrappel = $mtrappel;

        return $this;
    }

    public function getTelephone1(): ?string
    {
        return $this->telephone1;
    }

    public function setTelephone1(?string $telephone1): self
    {
        $this->telephone1 = $telephone1;

        return $this;
    }

    public function getInfoTel1(): ?string
    {
        return $this->infoTel1;
    }

    public function setInfoTel1(?string $infoTel1): self
    {
        $this->infoTel1 = $infoTel1;

        return $this;
    }

    public function getTelephone2(): ?string
    {
        return $this->telephone2;
    }

    public function setTelephone2(?string $telephone2): self
    {
        $this->telephone2 = $telephone2;

        return $this;
    }

    public function getInfoTel2(): ?string
    {
        return $this->infoTel2;
    }

    public function setInfoTel2(?string $infoTel2): self
    {
        $this->infoTel2 = $infoTel2;

        return $this;
    }

    public function getAdresseAdh(): ?string
    {
        return $this->adresseAdh;
    }

    public function setAdresseAdh(?string $adresseAdh): self
    {
        $this->adresseAdh = $adresseAdh;

        return $this;
    }

    public function getEmailAdh(): ?string
    {
        return $this->emailAdh;
    }

    public function setEmailAdh(?string $emailAdh): self
    {
        $this->emailAdh = $emailAdh;

        return $this;
    }

    public function getAssopartenaire(): ?bool
    {
        return $this->assopartenaire;
    }

    public function setAssopartenaire(bool $assopartenaire): self
    {
        $this->assopartenaire = $assopartenaire;

        return $this;
    }

    public function getCnrCrr(): ?bool
    {
        return $this->cnrCrr;
    }

    public function setCnrCrr(bool $cnrCrr): self
    {
        $this->cnrCrr = $cnrCrr;

        return $this;
    }


}
