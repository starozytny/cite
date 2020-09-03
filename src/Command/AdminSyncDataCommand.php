<?php

namespace App\Command;

use App\Entity\Cite\CiAdherent;
use App\Entity\Cite\CiPersonne;
use App\Entity\TicketProspect;
use App\Entity\TicketResponsable;
use App\Entity\Windev\WindevAdherent;
use App\Entity\Windev\WindevAncien;
use App\Entity\Windev\WindevPersonne;
use App\Service\Export;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AdminSyncDataCommand extends Command
{
    protected static $defaultName = 'admin:sync:data';
    private $em;
    private $export;
    const STATUS = TicketProspect::ST_CONFIRMED;

    public function __construct(EntityManagerInterface $entityManager, Export $export)
    {
        parent::__construct();

        $this->em = $entityManager;
        $this->export = $export;
    }

    protected function configure()
    {
        $this
            ->setDescription('Synchro data software and webservice')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        date_default_timezone_set('Europe/Paris');
        $io = new SymfonyStyle($input, $output);
         
        $io->title("Données originaux [PERSONNES]");
        $dataResponsables = $this->getDataOriPersonnes();

        $io->title("Données nouveaux [PERSONNES]");
        $dataNouveauxResponsables = $this->getDataNouveauxPersonnes();
        $dataResponsables = array_merge($dataResponsables, $dataNouveauxResponsables);

        $io->title("Données update [PERSONNES]");
        $dataUpdateResponsables = $this->getDataUpdatePersonnes($dataResponsables);
        $dataResponsables = array_replace($dataResponsables, $dataUpdateResponsables);

        $io->title("Création du fichier [PERSONNE]");
        $this->createFilePersonnes($dataResponsables);

        $io->newLine(2);
        $io->text('------- Completed !');

        return 0;
    }

    private function getDataUpdatePersonnes($dataResponsables){
        $em = $this->em;
        $prospects = $em->getRepository(TicketProspect::class)->findBy(array('status' => self::STATUS), array('id' => 'ASC'));

        $data = array();
        foreach ($prospects as $prospect){

            $passe = false;
            // RESPONSABLE saisie via le website
            $responsable = $prospect->getResponsable();

            if($prospect->getAdherent()){ // Si c'est un adhérent il a surement une PERSONNE
                if($prospect->getAdherent()->getPersonne()){ // Il a une PERSONNE on va update cette PERSONNE
                    $oldId = $prospect->getAdherent()->getPersonne()->getOldId();
                    $passe = true;
                }
            }else{
                $existe = $em->getRepository(CiAdherent::class)->findOneBy(array( // Cette année obligé car il y a peu etre des doublons vu qu'il n'y a pas de numAdh 
                    'firstname' => ucfirst(mb_strtolower($prospect->getFirstname())),
                    'lastname' => mb_strtoupper($prospect->getLastname())
                ));
    
                if($existe){
                    if($existe->getPersonne()){
                        $oldId = $existe->getPersonne()->getOldId();
                        $passe = true;
                    }
                }
            }

            if($passe){
                $personne = $em->getRepository(WindevPersonne::class)->find($oldId); // Get value Personne Windev
                $tmp = $this->getTmpPers($personne, $responsable, $personne->getId(), 1);
                $data[array_search($personne->getId(), array_column($dataResponsables, 0))] = $tmp; // ADD KEY INDEX FOR UPDATE DATARESPONSABLES
            }
        }

        return $data;
    }

    private function getDataNouveauxPersonnes(){
        $em = $this->em;
        $personnes = $em->getRepository(WindevPersonne::class)->findBy(array(), array('id' => 'ASC'));
        $prospects = $em->getRepository(TicketProspect::class)->findBy(array('status' => self::STATUS), array('id' => 'ASC'));

        // le dernier id de la table PERSONNE
        $lastID = $personnes[count($personnes)-1]->getId();

        $data = array();
        $noDoublon = array();
        foreach ($prospects as $prospect){

            $passe = true;
            if($prospect->getAdherent()){ // Si c'est un adhérent et qu'il a une personne il ne passe pas
                if($prospect->getAdherent()->getPersonne()){
                    $passe = false;
                }
            }else{
                $existe = $em->getRepository(CiAdherent::class)->findOneBy(array( // Cette année obligé car il y a peu etre des doublons vu qu'il n'y a pas de numAdh 
                    'firstname' => ucfirst(mb_strtolower($prospect->getFirstname())),
                    'lastname' => mb_strtoupper($prospect->getLastname())
                ));

                if($existe){
                    if($existe->getPersonne()){
                        $passe = false;
                    }
                }                
            }

            if($passe){ // S'il n'est pas adhérent ou adherent sans responsable(personne) = check si une PERSONNE existe est donc le RESPONSABLE sera la PERSONNE

                // RESPONSABLE saisie via le website
                $responsable = $prospect->getResponsable();

                // Check s'il existe une PERSONNE correspondant aux donnée du RESPONSABLE
                $personnesExistent = $em->getRepository(CiPersonne::class)->findBy(array(
                    'firstname' => mb_strtoupper($responsable->getLastname()),
                    'lastname' => ucfirst(mb_strtolower($responsable->getFirstname()))
                ));
                if(count($personnesExistent) != 1){ // S'il n'existe pas ou qu'il y a > 1 de résultats de PERSONNE => on créé ce nouveau PERSONNE
                    $phoneMobile = $this->formatPhone($responsable->getPhoneMobile());
                    $phoneDomicile = $this->formatPhone($responsable->getPhoneDomicile());

                    $lastID = $lastID + 1;

                    $tmp = array(
                        $lastID, 0, mb_strtoupper($responsable->getLastname()), ucfirst(mb_strtolower($responsable->getFirstname())), $this->getCivility($responsable->getCivility()),
                        $responsable->getAdr(), $responsable->getComplement(), $responsable->getCp(), mb_strtoupper($responsable->getCity()),
                        $phoneMobile, $phoneMobile != "" ? 'mobile' : '', $phoneDomicile, $phoneDomicile != "" ? 'domicile' : '',
                        null,0,null,0,null,null,null,null,0,0,null,null,null,null,null,null,null,null,null,null,null,null,null,null,$responsable->getEmail(), 0
                    );

                    $tmpNoId = $tmp;
                    array_shift($tmpNoId);

                    if(!in_array($tmpNoId, $noDoublon)){
                        array_push($data, $tmp);
                        array_push($noDoublon, $tmpNoId);
                    }
                }
            }
        }

        return $data;
    }

    private function getDataOriPersonnes()
    {
        $em = $this->em;
        $windevPersonnes = $em->getRepository(WindevPersonne::class)->findBy(array(), array('id' => 'ASC'));
        
        $data = array();
        foreach ($windevPersonnes as $pers){
            $tmp = $this->getTmpPers($pers,null,null, 1);
            if(!in_array($tmp, $data)){
                array_push($data, $tmp);
            }   
        }

        return $data;
    }

    private function getTmpPers($pers, $responsable=null, $oldId=null, $isExsite=0)
    {
        date_default_timezone_set('Europe/Paris');

        $id = $pers->getId();
        $lastname = $pers->getNom();
        $firstname = $pers->getPrenom();
        $civility = intval($pers->getTicleunik());
        $adr = $pers->getAdresse1();
        $complement = $pers->getAdresse2();
        $cp = $pers->getCdePostal();
        $city = $pers->getVille();
        $phone1 = $pers->getTelephone1();
        $name_phone1 = $pers->getInfoTel1();
        $phone2 = $pers->getTelephone2();
        $name_phone2 = $pers->getInfoTel2();
        $email = $pers->getEmailPers();

        if($responsable != null){
            $id = $oldId;
            $lastname = $responsable->getLastname();
            $firstname = $responsable->getFirstname();
            $civility = $this->getCivility($responsable->getCivility());
            $adr = $responsable->getAdr();
            $complement = $responsable->getComplement();
            $cp = $responsable->getCp();
            $city = $responsable->getCity();

            $phoneMobile = $this->formatPhone($responsable->getPhoneMobile());
            $phoneDomicile = $this->formatPhone($responsable->getPhoneDomicile());
            $name_phone1 = $phoneDomicile != "" ? 'domicile' : $pers->getInfoTel1();
            $phone1 = $phoneDomicile != "" ? $phoneDomicile : $pers->getTelephone1();
            $name_phone2 = $phoneMobile != "" ? 'mobile' : $pers->getInfoTel2();
            $phone2 = $phoneMobile != "" ? $phoneMobile : $pers->getTelephone2();

            $email = $responsable->getEmail();
        }

        $tmp = array(
            $id, intval($pers->getTycleunik()), $lastname, $firstname, $civility, $adr, $complement, $cp, $city, $phone1, 
            $name_phone1 , $phone2, $name_phone2, $pers->getNocompta(),intval($pers->getSfcleunik()),$pers->getNaissance(),intval($pers->getCacleunik()),$pers->getProfession(), $pers->getAdresseTrav(),$pers->getTelTrav(),
            $pers->getComment(),$pers->getMrcleunik(),$pers->getNbEch(), $pers->getBqDom1(),$pers->getBqDom2(),$pers->getBqCpte(),$pers->getBqCdebq(),$pers->getBqCdegu(),$pers->getBqClerib(),$pers->getTiret(),
            $pers->getInfoTelTra(),$pers->getTelephone3(),$pers->getInfoTel3(),$pers->getTelephone4(),$pers->getInfoTel4(),$pers->getTelephone5(),$pers->getInfoTel5(),$email, $isExsite
        );

        return $tmp;
    }

    private function createFilePersonnes($data)
    {
        $header = array(array('PECLEUNIK', 'TYCLEUNIK', 'NOM', 'PRENOM', 'TICLEUNIK', 'ADRESSE1', 'ADRESSE2', 'CDE_POSTAL', 'VILLE', 'TELEPHONE1', 'INFO_TEL1', 'TELEPHONE2', 'INFO_TEL2',
        'NOCOMPTA', 'SFCLEUNIK', 'NAISSANCE', 'CACLEUNIK', 'PROFESSION', 'ADRESSE_TRAV', 'TEL_TRAV', 'COMMENT', 'MRCLEUNIK', 'NB_ECH', 'BQ_DOM1', 'BQ_DOM2',
        'BQ_CPTE', 'BQ_CDEBQ', 'BQ_CDEGU', 'BQ_CLERIB', 'TIRET', 'INFO_TEL_TRA', 'TELEPHONE3', 'INFO_TEL3', 'TELEPHONE4', 'INFO_TEL4', 'TELEPHONE5', 'INFO_TEL5', 'EMAIL_PERS', 'IS_EXISTE'));
        $json = $this->export->createFile('csv', 'PERSONNE', 'PERSONNE.csv' , $header, $data, 39, null, 'synchro/');
    }

    private function getCivility($civ)
    {
        $civility = 2;
        if($civ == "Mr") {
            $civility = 5;
        }else if ($civ == "Mme") {
            $civility = 3;
        }

        return $civility;
    }

    private function formatPhone($value)
    {
        if(strlen($value) == 10){
            $a = substr($value, 0, 2);
            $b = substr($value, 2, 2);
            $c = substr($value, 4, 2);
            $d = substr($value, 6, 2);
            $e = substr($value, 8, 2);

            return $a . '.' . $b . '.' . $c . '.' . $d . '.' . $e;
        }
        return "";
    }
}
