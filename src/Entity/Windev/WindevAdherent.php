<?php

namespace App\Entity\Windev;

use App\Repository\Windev\WindevAdherentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * WindevAdherent
 * 
 * @ORM\Table(name="windev_adherent")
 * @ORM\Entity(repositoryClass="App\Repository\Windev\WindevAdherentRepository")
 */
class WindevAdherent
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
     * @var string|null
     *
     * @ORM\Column(name="NUM_FICHE", type="decimal", precision=38, scale=0, nullable=true)
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
     * @var string
     *
     * @ORM\Column(name="PRENOM", type="string", length=17, nullable=false)
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
     * @var string
     *
     * @ORM\Column(name="INSCRIPTION", type="decimal", precision=38, scale=0, nullable=false)
     */
    private $inscription;

    /**
     * @var string
     *
     * @ORM\Column(name="ADHESION", type="string", length=3, nullable=false)
     */
    private $adhesion;

    /**
     * @var string
     *
     * @ORM\Column(name="RENOUVELLEMENT", type="string", length=3, nullable=false)
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
     * @ORM\Column(name="COMMENT", type="string", length=248, nullable=true)
     */
    private $comment;

    /**
     * @var string
     *
     * @ORM\Column(name="NOTARIF", type="decimal", precision=38, scale=0, nullable=false)
     */
    private $notarif;

    /**
     * @var string
     *
     * @ORM\Column(name="DATECREATION", type="decimal", precision=38, scale=0, nullable=false)
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
     * @ORM\Column(name="MTRAPPEL", type="decimal", precision=38, scale=0, nullable=false)
     */
    private $mtrappel;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TELEPHONE1", type="string", length=15, nullable=true)
     */
    private $telephone1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="INFO_TEL1", type="string", length=6, nullable=true)
     */
    private $infoTel1;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="TELEPHONE2", type="boolean", nullable=true)
     */
    private $telephone2;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="INFO_TEL2", type="boolean", nullable=true)
     */
    private $infoTel2;

    /**
     * @var string|null
     *
     * @ORM\Column(name="EMAIL_ADH", type="string", length=32, nullable=true)
     */
    private $emailAdh;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ADRESSE_ADH", type="string", length=94, nullable=true)
     */
    private $adresseAdh;

    /**
     * @var bool
     *
     * @ORM\Column(name="FACTURER_ADR_PERSO", type="boolean", nullable=false)
     */
    private $facturerAdrPerso;

    /**
     * @var string
     *
     * @ORM\Column(name="MRCLEUNIK", type="decimal", precision=38, scale=0, nullable=false)
     */
    private $mrcleunik;

    /**
     * @var string
     *
     * @ORM\Column(name="NB_ECH", type="decimal", precision=38, scale=0, nullable=false)
     */
    private $nbEch;

    /**
     * @var string|null
     *
     * @ORM\Column(name="BQ_DOM1", type="string", length=28, nullable=true)
     */
    private $bqDom1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="BQ_DOM2", type="string", length=15, nullable=true)
     */
    private $bqDom2;

    /**
     * @var string|null
     *
     * @ORM\Column(name="BQ_CPTE", type="string", length=11, nullable=true)
     */
    private $bqCpte;

    /**
     * @var string|null
     *
     * @ORM\Column(name="BQ_CDEBQ", type="decimal", precision=38, scale=0, nullable=true)
     */
    private $bqCdebq;

    /**
     * @var string|null
     *
     * @ORM\Column(name="BQ_CDEGU", type="decimal", precision=38, scale=0, nullable=true)
     */
    private $bqCdegu;

    /**
     * @var string|null
     *
     * @ORM\Column(name="BQ_CLERIB", type="decimal", precision=38, scale=0, nullable=true)
     */
    private $bqClerib;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TIRET", type="string", length=30, nullable=true)
     */
    private $tiret;

    /**
     * @var bool
     *
     * @ORM\Column(name="MoyenEnvoiFacture", type="boolean", nullable=false)
     */
    private $moyenenvoifacture;

    /**
     * @var bool
     *
     * @ORM\Column(name="MoyenEnvoiFacture_2", type="boolean", nullable=false)
     */
    private $moyenenvoifacture2;

    /**
     * @var bool
     *
     * @ORM\Column(name="MoyenEnvoiFacture_3", type="boolean", nullable=false)
     */
    private $moyenenvoifacture3;

    /**
     * @var bool
     *
     * @ORM\Column(name="MoyenEnvoiAbsence", type="boolean", nullable=false)
     */
    private $moyenenvoiabsence;

    /**
     * @var bool
     *
     * @ORM\Column(name="MoyenEnvoiAbsence_2", type="boolean", nullable=false)
     */
    private $moyenenvoiabsence2;

    /**
     * @var bool
     *
     * @ORM\Column(name="MoyenEnvoiAbsence_3", type="boolean", nullable=false)
     */
    private $moyenenvoiabsence3;

    /**
     * @var bool
     *
     * @ORM\Column(name="MoyenEnvoiRelance", type="boolean", nullable=false)
     */
    private $moyenenvoirelance;

    /**
     * @var bool
     *
     * @ORM\Column(name="MoyenEnvoiRelance_2", type="boolean", nullable=false)
     */
    private $moyenenvoirelance2;

    /**
     * @var bool
     *
     * @ORM\Column(name="MoyenEnvoiRelance_3", type="boolean", nullable=false)
     */
    private $moyenenvoirelance3;

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

    /**
     * @var bool
     *
     * @ORM\Column(name="MajorationHM", type="boolean", nullable=false)
     */
    private $majorationhm;

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

    public function setNumFiche(?string $numFiche): self
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

    public function setPrenom(string $prenom): self
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

    public function setInscription(string $inscription): self
    {
        $this->inscription = $inscription;

        return $this;
    }

    public function getAdhesion(): ?string
    {
        return $this->adhesion;
    }

    public function setAdhesion(string $adhesion): self
    {
        $this->adhesion = $adhesion;

        return $this;
    }

    public function getRenouvellement(): ?string
    {
        return $this->renouvellement;
    }

    public function setRenouvellement(string $renouvellement): self
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

    public function setDatecreation(string $datecreation): self
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

    public function getTelephone2(): ?bool
    {
        return $this->telephone2;
    }

    public function setTelephone2(?bool $telephone2): self
    {
        $this->telephone2 = $telephone2;

        return $this;
    }

    public function getInfoTel2(): ?bool
    {
        return $this->infoTel2;
    }

    public function setInfoTel2(?bool $infoTel2): self
    {
        $this->infoTel2 = $infoTel2;

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

    public function getAdresseAdh(): ?string
    {
        return $this->adresseAdh;
    }

    public function setAdresseAdh(?string $adresseAdh): self
    {
        $this->adresseAdh = $adresseAdh;

        return $this;
    }

    public function getFacturerAdrPerso(): ?bool
    {
        return $this->facturerAdrPerso;
    }

    public function setFacturerAdrPerso(bool $facturerAdrPerso): self
    {
        $this->facturerAdrPerso = $facturerAdrPerso;

        return $this;
    }

    public function getMrcleunik(): ?string
    {
        return $this->mrcleunik;
    }

    public function setMrcleunik(string $mrcleunik): self
    {
        $this->mrcleunik = $mrcleunik;

        return $this;
    }

    public function getNbEch(): ?string
    {
        return $this->nbEch;
    }

    public function setNbEch(string $nbEch): self
    {
        $this->nbEch = $nbEch;

        return $this;
    }

    public function getBqDom1(): ?string
    {
        return $this->bqDom1;
    }

    public function setBqDom1(?string $bqDom1): self
    {
        $this->bqDom1 = $bqDom1;

        return $this;
    }

    public function getBqDom2(): ?string
    {
        return $this->bqDom2;
    }

    public function setBqDom2(?string $bqDom2): self
    {
        $this->bqDom2 = $bqDom2;

        return $this;
    }

    public function getBqCpte(): ?string
    {
        return $this->bqCpte;
    }

    public function setBqCpte(?string $bqCpte): self
    {
        $this->bqCpte = $bqCpte;

        return $this;
    }

    public function getBqCdebq(): ?string
    {
        return $this->bqCdebq;
    }

    public function setBqCdebq(?string $bqCdebq): self
    {
        $this->bqCdebq = $bqCdebq;

        return $this;
    }

    public function getBqCdegu(): ?string
    {
        return $this->bqCdegu;
    }

    public function setBqCdegu(?string $bqCdegu): self
    {
        $this->bqCdegu = $bqCdegu;

        return $this;
    }

    public function getBqClerib(): ?string
    {
        return $this->bqClerib;
    }

    public function setBqClerib(?string $bqClerib): self
    {
        $this->bqClerib = $bqClerib;

        return $this;
    }

    public function getTiret(): ?string
    {
        return $this->tiret;
    }

    public function setTiret(?string $tiret): self
    {
        $this->tiret = $tiret;

        return $this;
    }

    public function getMoyenenvoifacture(): ?bool
    {
        return $this->moyenenvoifacture;
    }

    public function setMoyenenvoifacture(bool $moyenenvoifacture): self
    {
        $this->moyenenvoifacture = $moyenenvoifacture;

        return $this;
    }

    public function getMoyenenvoifacture2(): ?bool
    {
        return $this->moyenenvoifacture2;
    }

    public function setMoyenenvoifacture2(bool $moyenenvoifacture2): self
    {
        $this->moyenenvoifacture2 = $moyenenvoifacture2;

        return $this;
    }

    public function getMoyenenvoifacture3(): ?bool
    {
        return $this->moyenenvoifacture3;
    }

    public function setMoyenenvoifacture3(bool $moyenenvoifacture3): self
    {
        $this->moyenenvoifacture3 = $moyenenvoifacture3;

        return $this;
    }

    public function getMoyenenvoiabsence(): ?bool
    {
        return $this->moyenenvoiabsence;
    }

    public function setMoyenenvoiabsence(bool $moyenenvoiabsence): self
    {
        $this->moyenenvoiabsence = $moyenenvoiabsence;

        return $this;
    }

    public function getMoyenenvoiabsence2(): ?bool
    {
        return $this->moyenenvoiabsence2;
    }

    public function setMoyenenvoiabsence2(bool $moyenenvoiabsence2): self
    {
        $this->moyenenvoiabsence2 = $moyenenvoiabsence2;

        return $this;
    }

    public function getMoyenenvoiabsence3(): ?bool
    {
        return $this->moyenenvoiabsence3;
    }

    public function setMoyenenvoiabsence3(bool $moyenenvoiabsence3): self
    {
        $this->moyenenvoiabsence3 = $moyenenvoiabsence3;

        return $this;
    }

    public function getMoyenenvoirelance(): ?bool
    {
        return $this->moyenenvoirelance;
    }

    public function setMoyenenvoirelance(bool $moyenenvoirelance): self
    {
        $this->moyenenvoirelance = $moyenenvoirelance;

        return $this;
    }

    public function getMoyenenvoirelance2(): ?bool
    {
        return $this->moyenenvoirelance2;
    }

    public function setMoyenenvoirelance2(bool $moyenenvoirelance2): self
    {
        $this->moyenenvoirelance2 = $moyenenvoirelance2;

        return $this;
    }

    public function getMoyenenvoirelance3(): ?bool
    {
        return $this->moyenenvoirelance3;
    }

    public function setMoyenenvoirelance3(bool $moyenenvoirelance3): self
    {
        $this->moyenenvoirelance3 = $moyenenvoirelance3;

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

    public function getMajorationhm(): ?bool
    {
        return $this->majorationhm;
    }

    public function setMajorationhm(bool $majorationhm): self
    {
        $this->majorationhm = $majorationhm;

        return $this;
    }


}
