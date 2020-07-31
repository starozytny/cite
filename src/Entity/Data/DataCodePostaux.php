<?php

namespace App\Entity\Data;

use Doctrine\ORM\Mapping as ORM;

/**
 * DataCodePostaux
 *
 * @ORM\Table(name="data_code_postaux")
 * @ORM\Entity(repositoryClass="App\Repository\Data\DataCodePostauxRepository")
 */
class DataCodePostaux
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
     * @ORM\Column(name="Code_commune_INSEE", type="string", length=5, nullable=false)
     */
    private $codeCommuneInsee;

    /**
     * @var string
     *
     * @ORM\Column(name="Nom_commune", type="string", length=38, nullable=false)
     */
    private $nomCommune;

    /**
     * @var string
     *
     * @ORM\Column(name="Code_postal", type="decimal", precision=38, scale=0, nullable=false)
     */
    private $codePostal;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Ligne_5", type="string", length=33, nullable=true)
     */
    private $ligne5;

    /**
     * @var string
     *
     * @ORM\Column(name="Libellé_d_acheminement", type="string", length=32, nullable=false)
     */
    private $libell�DAcheminement;

    /**
     * @var string|null
     *
     * @ORM\Column(name="coordonnees_gps", type="string", length=32, nullable=true)
     */
    private $coordonneesGps;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeCommuneInsee(): ?string
    {
        return $this->codeCommuneInsee;
    }

    public function setCodeCommuneInsee(string $codeCommuneInsee): self
    {
        $this->codeCommuneInsee = $codeCommuneInsee;

        return $this;
    }

    public function getNomCommune(): ?string
    {
        return $this->nomCommune;
    }

    public function setNomCommune(string $nomCommune): self
    {
        $this->nomCommune = $nomCommune;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getLigne5(): ?string
    {
        return $this->ligne5;
    }

    public function setLigne5(?string $ligne5): self
    {
        $this->ligne5 = $ligne5;

        return $this;
    }

    public function getLibell�DAcheminement(): ?string
    {
        return $this->libell�DAcheminement;
    }

    public function setLibell�DAcheminement(string $libell�DAcheminement): self
    {
        $this->libell�DAcheminement = $libell�DAcheminement;

        return $this;
    }

    public function getCoordonneesGps(): ?string
    {
        return $this->coordonneesGps;
    }

    public function setCoordonneesGps(?string $coordonneesGps): self
    {
        $this->coordonneesGps = $coordonneesGps;

        return $this;
    }


}
