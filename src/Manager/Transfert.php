<?php


namespace App\Manager;

use App\Entity\Cite\CiAdherent;
use App\Entity\Cite\CiPersonne;
use App\Entity\Windev\WindevPersonne;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

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

    protected function getCivility($item)
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

        return $title;
    }

    protected function getPrenomNom($item){
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
        return [
            $nom,
            ucfirst(mb_strtolower($prenom))
        ];
    }

    public function createPersonne(WindevPersonne $item)
    {
        
    
        $cp = strlen($item->getCdePostal()) < 5 ? 0 . $item->getCdePostal() : $item->getCdePostal();
        

        return (new CiPersonne())
            ->setOldId($item->getId())
            ->setLastname($this->getPrenomNom($item)[0])
            ->setFirstname($this->getPrenomNom($item)[1])
            ->setCivility($this->getCivility($item))
            ->setEmail($item->getEmailPers())
            ->setAdr($item->getAdresse1())
            ->setComplement($item->getAdresse2())
            ->setCp($cp)
            ->setCity($item->getVille())
            ->setPhoneMobile($this->formattedPhone($item->getTelephone1()))
            ->setPhoneDomicile($this->formattedPhone($item->getTelephone2()))
            ;
    }

    public function createAdherent($item, $ancien)
    {
        // $numAdh = $ancien ? $item->getNumFiche() : ($item->getNumFiche() != null ? $item->getNumFiche() : $item->getId());
        $numAdh = $ancien ? $item->getNumFiche() : $item->getId();

        if($this->em->getRepository(CiAdherent::class)->findOneBy(array('numAdh' => $numAdh))){
            throw new Exception("Erreur - Numero adherent non unique : " . $numAdh);
        }

        return (new CiAdherent())
            ->setOldId($item->getId())
            ->setCivility($this->getCivility($item))
            ->setLastname($this->getPrenomNom($item)[0])
            ->setFirstname($this->getPrenomNom($item)[1])
            ->setEmail($item->getEmailAdh())
            ->setBirthday($this->createDate($item->getNaissance()))
            ->setNumAdh($numAdh)
            ->setIsAncien($ancien)
            ->setAdr($item->getAdresseAdh())
            ->setPhoneMobile($this->formattedPhone($item->getTelephone1()))
            ->setPhoneDomicile($this->formattedPhone($item->getTelephone2()))
            ->setPersonne($this->em->getRepository(CiPersonne::class)->findOneBy(array('id' => $item->getPecleunik())))
        ;
    }
}
