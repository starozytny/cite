<?php

namespace App\Command;

use App\Entity\TicketResponsable;
use App\Entity\Windev\WindevAdherent;
use App\Entity\Windev\WindevAncien;
use App\Entity\Windev\WindevPersonne;
use App\Service\Export;
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

    private function getCivility($civ){
        $civility = 2;
        if($civ == "Mr") {
            $civility = 5;
        }else if ($civ == "Mme") {
            $civility = 3;
        }

        return $civility;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $em = $this->em;
        $export = $this->export;
        $responsables = $em->getRepository(TicketResponsable::class)->findAll();
        $personnes = $em->getRepository(WindevPersonne::class)->findBy(array(), array('id' => 'ASC'));
        $adherents = $em->getRepository(WindevAdherent::class)->findBy(array(), array('id' => 'ASC'));
        
        $data = array();
        $data2 = array();
        $dataAdd = array();
        $dataAdd2 = array();
        $constOldId = $personnes[count($personnes)-1]->getId();


        $io->title("Données originaux [PERSONNE]");
        $progressBar = new ProgressBar($output, count($personnes));
        $progressBar->setFormat("%current%/%max% [%bar%] %percent:3s%%  🏁");
        //DATA ORIGINAUX
        $progressBar->start();
        foreach ($personnes as $pers){
            $progressBar->advance();
            $tmp = $this->getTmpPers($pers);
            if(!in_array($tmp, $data)){
                array_push($data, $tmp);
            }   
        }
        $io->newLine(2);
        $io->title("Données originaux [ADH]");
        $progressBar = new ProgressBar($output, count($adherents));
        foreach ($adherents as $adh){
            $progressBar->advance();
            $tmp =  $this->getTmpAdh($adh, $adh->getPecleunik());
            if(!in_array($tmp, $data2)){
                array_push($data2, $tmp);
            }   
        }
        $progressBar->finish();

        //UPDATE OR NEW DATA
        $lastkeyArray = array_key_last($data) + 2;
        foreach ($responsables as $responsable) {
            $prospects = $responsable->getProspects();
            if(count($prospects) > 0 && $responsable->getStatus() != TicketResponsable::ST_TMP){

                $phoneMobile = $this->formatPhone($responsable->getPhoneMobile());
                $phoneDomicile = $this->formatPhone($responsable->getPhoneDomicile());

                $dataPers = null; $tmp2 = null;
                if($prospects[0]->getAdherent()){
                    $dataPers = $prospects[0]->getAdherent()->getPersonne();
                    if(count($prospects) >= 1){
                        foreach ($prospects as $prospect){
                            if($prospect->getAdherent()){
                                if($dataPers != $prospect->getAdherent()->getPersonne()){
                                    $dataPers = null;
                                }
                                if(!$prospect->getAdherent()->getIsAncien()){
                                    $adh = $em->getRepository(WindevAdherent::class)->find($prospect->getAdherent()->getOldId());
                                    $tmp2 = $this->getTmpAdh($adh, $adh->getPecleunik(), $prospect);
                                    $dataAdd2[array_search($prospect->getAdherent()->getOldId(), array_column($data2, 0))] = $tmp2; // ADD KEY INDEX OF UPDATE DATA
                                }else{
                                    $adh = $em->getRepository(WindevAncien::class)->find($prospect->getAdherent()->getOldId());
                                }
                                
                            }
                        }
                    }
                    
                    //IF PERSONNE EXISTE = UPDATE
                    if($dataPers != null){
                        $personne = $em->getRepository(WindevPersonne::class)->find($dataPers->getOldId());
                        $nameDomicile = $phoneDomicile != "" ? 'domicile' : $personne->getInfoTel1();
                        $phoneDomicile = $phoneDomicile != "" ? $phoneDomicile : $personne->getTelephone1();
                        $nameMobile = $phoneMobile != "" ? 'mobile' : $personne->getInfoTel2();
                        $phoneMobile = $phoneMobile != "" ? $phoneMobile : $personne->getTelephone2();
                        $oldId = $personne->getId();
                        $tmp = $this->getTmpPers($personne, $responsable, $oldId, $phoneMobile, $phoneDomicile, $nameMobile, $nameDomicile);
                    }
                }
                
                if($dataPers == null){ // IF PERSONNE NOT EXISTE = NEW -> ADD LAST KEY INDEX 
                    $nameMobile = $phoneMobile != "" ? 'mobile' : '';
                    $nameDomicile = $phoneDomicile != "" ? 'domicile' : '';
                    $constOldId = $constOldId + 1;
                    $oldId = $constOldId;
                    $tmp = array(
                        $oldId, 0, $responsable->getLastname(), $responsable->getFirstname(), $this->getCivility($responsable->getCivility()),
                        $responsable->getAdr(), $responsable->getComplement(), $responsable->getCp(), $responsable->getCity(),
                        $phoneMobile, $nameMobile, $phoneDomicile, $nameDomicile,
                        null,0,null,0,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,$responsable->getEmail()
                    );

                    if(!in_array($tmp, $dataAdd)){
                        array_push($dataAdd, $tmp);
                    }
                    $lastkeyArray = $lastkeyArray+1;
                    $dataAdd[$lastkeyArray] = $tmp;
                }else{
                    $dataAdd[array_search($oldId, array_column($data, 0))] = $tmp; // ADD KEY INDEX OF UPDATE DATA
                }
            }
        }  

        $data = array_replace($data, $dataAdd);
        $data2 = array_replace($data2, $dataAdd2);

        $io->newLine(2);
        $io->text('------- Completed !');

        // $fileName = 'PERSONNE.csv';
        // $header = array(array('PECLEUNIK', 'TYCLEUNIK', 'NOM', 'PRENOM', 'TICLEUNIK', 'ADRESSE1', 'ADRESSE2', 'CDE_POSTAL', 'VILLE', 'TELEPHONE1', 'INFO_TEL1', 'TELEPHONE2', 'INFO_TEL2',
        //                       'NOCOMPTA', 'SFCLEUNIK', 'NAISSANCE', 'CACLEUNIK', 'PROFESSION', 'ADRESSE_TRAV', 'TEL_TRAV', 'COMMENT', 'MRCLEUNIK', 'NB_ECH', 'BQ_DOM1', 'BQ_DOM2',
        //                       'BQ_CPTE', 'BQ_CDBEDQ', 'BQ_CDEGU', 'BQ_CLERIB', 'TIRET', 'INFO_TEL_TRA', 'TELEPHONE3', 'INFO_TEL3', 'TELEPHONE4', 'INFO_TEL4', 'TELEPHONE5', 'INFO_TEL5', 'EMAIL_PERS'));
        // $json = $export->createFile('csv', 'PERSONNE', $fileName , $header, $data, 38, null, 'synchro/');
        $fileName = 'ADHERENT.csv';
        $header = array(array('ADCLEUNIK', 'PECLEUNIK', 'NUM_FICHE', 'NUM_FAMILLE', 'NOM', 'PRENOM', 'TICLEUNIK', 'NAISSANCE', 'SEXE', 'CARTEADHERENT', 'TYCLEUNIK', 'INSCRIPTION', 'ADHESION', 'RENOUVELLEMENT', 'SORTIE',
                              'NOCOMPTA', 'CECLEUNIK', 'COMMENT', 'NOTARIF', 'DATECREATION', 'DATEMAJ', 'NORAPPEL', 'LIENPROFESSEUR', 'DISPSOLFEGE', 'MTRAPPEL', 'TELEPHONE1', 'INFO_TEL1', 'TELEPHONE2', 'INFO_TEL2', 'EMAIL_ADH',
                              'EMAIL_ADH', 'ADRESSE_ADH', 'FACTURER_ADR_PERSO', 'MRCLEUNIK', 'NB_ECH', 'BQ_DOM1', 'BQ_DOM2', 'BQ_CPTE', 'BQ_CDBEDQ', 'BQ_CDEGU', 'BQ_CLERIB', 'TIRET', 
                              'MoyenEnvoiFacture', 'MoyenEnvoiFacture_2', 'MoyenEnvoiFacture_3', 'MoyenEnvoiAbsence', 'MoyenEnvoiAbsence_2', 'MoyenEnvoiAbsence_3', 'MoyenEnvoiRelance',
                              'MoyenEnvoiRelance_2', 'MoyenEnvoiRelance_3', 'AssoPartenaire', 'CNR_CRR', 'MajorationHM'));
        $json = $export->createFile('csv', 'ADHERENT', $fileName , $header, $data2, 53, null, 'synchro/');
        return 0;
    }

    private function getTmpPers($pers, $responsable=null, $oldId=null, $phoneMobile=null, $phoneDomicile=null, $nameMobile=null, $nameDomicile=null){
        $id=$pers->getId();
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
            $phone1 = $phoneDomicile;
            $name_phone1 = $nameDomicile;
            $phone2 = $phoneMobile;
            $name_phone2 = $nameMobile;
            $email = $responsable->getEmail();
        }

        $tmp = array(
            $id, intval($pers->getTycleunik()), $lastname, $firstname, $civility, $adr, $complement, $cp, $city, $phone1, $name_phone1 , $phone2, $name_phone2,
            $pers->getNocompta(),intval($pers->getSfcleunik()),$pers->getNaissance(),intval($pers->getCacleunik()),$pers->getProfession(),
            $pers->getAdresseTrav(),$pers->getTelTrav(),$pers->getComment(),$pers->getMrcleunik(),$pers->getNbEch(),
            $pers->getBqDom1(),$pers->getBqDom2(),$pers->getBqCpte(),$pers->getBqCdebq(),$pers->getBqCdegu(),$pers->getBqClerib(),$pers->getTiret(),
            $pers->getInfoTelTra(),$pers->getTelephone3(),$pers->getInfoTel3(),$pers->getTelephone4(),$pers->getInfoTel4(),$pers->getTelephone5(),$pers->getInfoTel5(),
            $email
        );

        return $tmp;
    }

    private function getTmpAdh($adh, $personneId, $pro=null){
        $nom = $adh->getNom();
        $prenom = $adh->getPrenom();
        $civility = intval($adh->getTicleunik());

        if($pro != null){
            $nom = $pro->getLastname();
            $prenom = $pro->getFirstname();
            $civility = $this->getCivility($pro->getCivility());
        }
        $tmp = array(
            $adh->getId(), $personneId, intval($adh->getNumFiche()), intval($adh->getNumFamille()),$nom , $prenom, $civility, $adh->getNaissance(),
            $adh->getSexe(), $adh->getCarteadherent(), $adh->getTycleunik(), $adh->getInscription(), $adh->getAdhesion(), $adh->getRenouvellement(), $adh->getSortie(),
            $adh->getNocompta(), $adh->getCecleunik(), $adh->getComment(), $adh->getNotarif(), $adh->getDatecreation(), $adh->getDatemaj(), $adh->getNorappel(), $adh->getLienprofesseur(),
            $adh->getDispsolfege(), $adh->getMtrappel(), $adh->getTelephone1(), $adh->getInfoTel1(), $adh->getTelephone2(), $adh->getInfoTel2(), $adh->getEmailAdh(), $adh->getAdresseAdh(),
            $adh->getFacturerAdrPerso(), $adh->getMrcleunik(), $adh->getNbEch(),
            $adh->getBqDom1(),$adh->getBqDom2(),$adh->getBqCpte(),$adh->getBqCdebq(),$adh->getBqCdegu(),$adh->getBqClerib(),$adh->getTiret(),
            $adh->getMoyenenvoifacture(), $adh->getMoyenenvoifacture2(),$adh->getMoyenenvoifacture3(), $adh->getMoyenenvoiabsence(),$adh->getMoyenenvoiabsence2(), $adh->getMoyenenvoiabsence3(),
            $adh->getMoyenenvoirelance(), $adh->getMoyenenvoirelance2(),$adh->getMoyenenvoirelance3(), $adh->getAssopartenaire(), $adh->getCnrCrr(), $adh->getMajorationhm()
        );

        return $tmp;
    }

    private function formatPhone($value){
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
