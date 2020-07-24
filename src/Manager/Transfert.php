<?php


namespace App\Manager;

use App\Entity\Cite\CiPersonne;
use App\Entity\Cite\Personne;
use App\Entity\Windev\WindevPersonne;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class Transfert
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    protected function createDate($date)
    {
        $a = substr($date, 0,4);
        $b = substr($date, 4,2);
        $c = substr($date, 6,2);

        $dateFormat = DateTime::createFromFormat('Y-m-d',$a.'-'.$b.'-'.$c);
        if($dateFormat == false){
            return null;
        }
        return $dateFormat;
    }

    protected function formattedPhone($value){
        return $value != null ? str_replace('.' , '', $value) : null;
    }

    public function createPersonne(WindevPersonne $item)
    {
        $title = CiPersonne::TITLE_UNKNOWN;
        switch ($item->getTicleunik()){
            case 1:
            case 3:
                $title = CiPersonne::TITLE_MME;
                break;
            case 2:
            case 5:
                $title = CiPersonne::TITLE_MR;
                break;
            default:
                break;
        }

        $nom = $item->getNom();
        $prenom = $item->getPrenom();
        if($prenom == null){
            $pos = strrpos($nom, ' ');
            if($pos != false){
                $prenom = substr($nom, $pos+1, strlen($nom));
                $nom = substr($nom, 0, $pos);
            }else{
                $prenom = $nom;
            }
        }
        $prenom = ucfirst(mb_strtolower($prenom));

        $cp = strlen($item->getCdePostal()) < 5 ? 0 . $item->getCdePostal() : $item->getCdePostal();
        

        return (new CiPersonne())
            ->setOldId($item->getId())
            ->setLastname($nom)
            ->setFirstname($prenom)
            ->setCivility($title)
            ->setEmail($item->getEmailPers())
            ->setAdr($item->getAdresse1())
            ->setComplement($item->getAdresse2())
            ->setCp($cp)
            ->setCity($item->getVille())
            ->setPhoneMobile($this->formattedPhone($item->getTelephone1()))
            ->setPhoneDomicile($this->formattedPhone($item->getTelephone2()))
            ;
    }

    // public function createMember(Adherent $item)
    // {
    //     $information = (new Information())
    //         ->setAdr($item->getAdresseAdh())
    //         ->setPhone1($item->getTelephone1())
    //         ->setPhone2($item->getTelephone2())
    //         ->setInfoPhone1($item->getInfoTel1())
    //         ->setInfoPhone2($item->getInfoTel2())
    //     ;

    //     $this->em->persist($information);

    //     $center = $this->em->getRepository(Center::class)->findOneBy(array(
    //         'id' => $item->getCecleunik()
    //     ));

    //     $createAt = $this->createDate($item->getDatecreation());
    //     $birthday = $this->createDate($item->getNaissance());
    //     $inscription = $this->createDate($item->getInscription());

    //     return (new Member())
    //         ->setOldId($item->getId())
    //         ->setLastname($item->getNom())
    //         ->setFirstname($item->getPrenom())
    //         ->setEmail($item->getEmailAdh())
    //         ->setComment($item->getComment())
    //         ->setCreateAt($createAt)
    //         ->setBirthday($birthday)
    //         ->setInscription($inscription)
    //         ->setCarteAdh($item->getCarteadherent())
    //         ->setDispenseSolfege($item->getDispsolfege())
    //         ->setNumCompta($item->getNocompta())
    //         ->setNumFamille($item->getNumFamille())
    //         ->setNumFiche($item->getNumFiche())
    //         ->setNumTarif($item->getNotarif())
    //         ->setRenouvellement($item->getRenouvellement())
    //         ->setSex($item->getSexe())
    //         ->setInformation($information)
    //         ->setCenter($center)
    //         ->setPersonne($this->em->getRepository(Personne::class)->findOneBy(array('old_id' => $item->getId())))
    //     ;
    // }
}
