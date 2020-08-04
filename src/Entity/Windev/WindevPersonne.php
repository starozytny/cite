<?php

namespace App\Entity\Windev;

use Doctrine\ORM\Mapping as ORM;

/**
 * WindevPersonne
 * 
 * @ORM\Table(name="windev_personne")
 * @ORM\Entity(repositoryClass="App\Repository\Windev\WindevPersonneRepository")
 */
class WindevPersonne
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
     * @var bool
     *
     * @ORM\Column(name="TYCLEUNIK", type="boolean", nullable=false)
     */
    private $tycleunik;

    /**
     * @var string
     *
     * @ORM\Column(name="NOM", type="string", length=30, nullable=false)
     */
    private $nom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="PRENOM", type="string", length=17, nullable=true)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="TICLEUNIK", type="decimal", precision=38, scale=0, nullable=false)
     */
    private $ticleunik;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ADRESSE1", type="string", length=40, nullable=true)
     */
    private $adresse1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ADRESSE2", type="string", length=40, nullable=true)
     */
    private $adresse2;

    /**
     * @var string|null
     *
     * @ORM\Column(name="CDE_POSTAL", type="decimal", precision=38, scale=0, nullable=true)
     */
    private $cdePostal;

    /**
     * @var string|null
     *
     * @ORM\Column(name="VILLE", type="string", length=26, nullable=true)
     */
    private $ville;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TELEPHONE1", type="string", length=17, nullable=true)
     */
    private $telephone1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="INFO_TEL1", type="string", length=24, nullable=true)
     */
    private $infoTel1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TELEPHONE2", type="string", length=17, nullable=true)
     */
    private $telephone2;

    /**
     * @var string|null
     *
     * @ORM\Column(name="INFO_TEL2", type="string", length=24, nullable=true)
     */
    private $infoTel2;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="NOCOMPTA", type="boolean", nullable=true)
     */
    private $nocompta;

    /**
     * @var bool
     *
     * @ORM\Column(name="SFCLEUNIK", type="boolean", nullable=false)
     */
    private $sfcleunik;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="NAISSANCE", type="boolean", nullable=true)
     */
    private $naissance;

    /**
     * @var bool
     *
     * @ORM\Column(name="CACLEUNIK", type="boolean", nullable=false)
     */
    private $cacleunik;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="PROFESSION", type="boolean", nullable=true)
     */
    private $profession;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ADRESSE_TRAV", type="string", length=102, nullable=true)
     */
    private $adresseTrav;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TEL_TRAV", type="string", length=17, nullable=true)
     */
    private $telTrav;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="COMMENT", type="boolean", nullable=true)
     */
    private $comment;

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
     * @ORM\Column(name="BQ_DOM1", type="string", length=30, nullable=true)
     */
    private $bqDom1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="BQ_DOM2", type="string", length=25, nullable=true)
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
     * @ORM\Column(name="BQ_CDEBQ", type="string", length=5, nullable=true)
     */
    private $bqCdebq;

    /**
     * @var string|null
     *
     * @ORM\Column(name="BQ_CDEGU", type="string", length=5, nullable=true)
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
     * @var string|null
     *
     * @ORM\Column(name="INFO_TEL_TRA", type="string", length=22, nullable=true)
     */
    private $infoTelTra;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TELEPHONE3", type="string", length=14, nullable=true)
     */
    private $telephone3;

    /**
     * @var string|null
     *
     * @ORM\Column(name="INFO_TEL3", type="string", length=12, nullable=true)
     */
    private $infoTel3;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TELEPHONE4", type="string", length=14, nullable=true)
     */
    private $telephone4;

    /**
     * @var string|null
     *
     * @ORM\Column(name="INFO_TEL4", type="string", length=12, nullable=true)
     */
    private $infoTel4;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="TELEPHONE5", type="boolean", nullable=true)
     */
    private $telephone5;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="INFO_TEL5", type="boolean", nullable=true)
     */
    private $infoTel5;

    /**
     * @var string|null
     *
     * @ORM\Column(name="EMAIL_PERS", type="string", length=43, nullable=true)
     */
    private $emailPers;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTicleunik(): ?string
    {
        return $this->ticleunik;
    }

    public function setTicleunik(string $ticleunik): self
    {
        $this->ticleunik = $ticleunik;

        return $this;
    }

    public function getAdresse1(): ?string
    {
        return $this->adresse1;
    }

    public function setAdresse1(?string $adresse1): self
    {
        $this->adresse1 = $adresse1;

        return $this;
    }

    public function getAdresse2(): ?string
    {
        return $this->adresse2;
    }

    public function setAdresse2(?string $adresse2): self
    {
        $this->adresse2 = $adresse2;

        return $this;
    }

    public function getCdePostal(): ?string
    {
        return $this->cdePostal;
    }

    public function setCdePostal(?string $cdePostal): self
    {
        $this->cdePostal = $cdePostal;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): self
    {
        $this->ville = $ville;

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

    public function getNocompta(): ?bool
    {
        return $this->nocompta;
    }

    public function setNocompta(?bool $nocompta): self
    {
        $this->nocompta = $nocompta;

        return $this;
    }

    public function getSfcleunik(): ?bool
    {
        return $this->sfcleunik;
    }

    public function setSfcleunik(bool $sfcleunik): self
    {
        $this->sfcleunik = $sfcleunik;

        return $this;
    }

    public function getNaissance(): ?bool
    {
        return $this->naissance;
    }

    public function setNaissance(?bool $naissance): self
    {
        $this->naissance = $naissance;

        return $this;
    }

    public function getCacleunik(): ?bool
    {
        return $this->cacleunik;
    }

    public function setCacleunik(bool $cacleunik): self
    {
        $this->cacleunik = $cacleunik;

        return $this;
    }

    public function getProfession(): ?bool
    {
        return $this->profession;
    }

    public function setProfession(?bool $profession): self
    {
        $this->profession = $profession;

        return $this;
    }

    public function getAdresseTrav(): ?string
    {
        return $this->adresseTrav;
    }

    public function setAdresseTrav(?string $adresseTrav): self
    {
        $this->adresseTrav = $adresseTrav;

        return $this;
    }

    public function getTelTrav(): ?string
    {
        return $this->telTrav;
    }

    public function setTelTrav(?string $telTrav): self
    {
        $this->telTrav = $telTrav;

        return $this;
    }

    public function getComment(): ?bool
    {
        return $this->comment;
    }

    public function setComment(?bool $comment): self
    {
        $this->comment = $comment;

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

    public function getInfoTelTra(): ?string
    {
        return $this->infoTelTra;
    }

    public function setInfoTelTra(?string $infoTelTra): self
    {
        $this->infoTelTra = $infoTelTra;

        return $this;
    }

    public function getTelephone3(): ?string
    {
        return $this->telephone3;
    }

    public function setTelephone3(?string $telephone3): self
    {
        $this->telephone3 = $telephone3;

        return $this;
    }

    public function getInfoTel3(): ?string
    {
        return $this->infoTel3;
    }

    public function setInfoTel3(?string $infoTel3): self
    {
        $this->infoTel3 = $infoTel3;

        return $this;
    }

    public function getTelephone4(): ?string
    {
        return $this->telephone4;
    }

    public function setTelephone4(?string $telephone4): self
    {
        $this->telephone4 = $telephone4;

        return $this;
    }

    public function getInfoTel4(): ?string
    {
        return $this->infoTel4;
    }

    public function setInfoTel4(?string $infoTel4): self
    {
        $this->infoTel4 = $infoTel4;

        return $this;
    }

    public function getTelephone5(): ?bool
    {
        return $this->telephone5;
    }

    public function setTelephone5(?bool $telephone5): self
    {
        $this->telephone5 = $telephone5;

        return $this;
    }

    public function getInfoTel5(): ?bool
    {
        return $this->infoTel5;
    }

    public function setInfoTel5(?bool $infoTel5): self
    {
        $this->infoTel5 = $infoTel5;

        return $this;
    }

    public function getEmailPers(): ?string
    {
        return $this->emailPers;
    }

    public function setEmailPers(?string $emailPers): self
    {
        $this->emailPers = $emailPers;

        return $this;
    }


}
