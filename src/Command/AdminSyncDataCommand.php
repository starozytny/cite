<?php

namespace App\Command;

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
        $lastkeyArrayAdherent = array_key_last($data2) + 2;
        foreach ($responsables as $responsable) {
            $prospects = $responsable->getProspects();
            if(count($prospects) > 0 && $responsable->getStatus() != TicketResponsable::ST_TMP){

                $registered = false;
                $dataPers = null; $tmp2 = null;
                if($prospects[0]->getAdherent()){
                    $dataPers = $prospects[0]->getAdherent()->getPersonne();
                    if(count($prospects) >= 1){
                        foreach ($prospects as $prospect){
                            if($prospect->getStatus() == TicketProspect::ST_REGISTERED){
                                $registered = true;
                                if($prospect->getAdherent()){
                                    if($dataPers != $prospect->getAdherent()->getPersonne()){
                                        $dataPers = null;
                                    }
                                    if($prospect->getAdherent()->getIsAncien() == false){
                                        $adh = $em->getRepository(WindevAdherent::class)->findOneBy(array('id' => $prospect->getAdherent()->getOldId()));
                                        $tmp2 = $this->getTmpAdh($adh, $adh->getPecleunik(), $prospect, 0);
                                        $dataAdd2[array_search($prospect->getAdherent()->getOldId(), array_column($data2, 0))] = $tmp2; // ADD KEY INDEX OF UPDATE DATA
                                    }else{
                                        $adh = $em->getRepository(WindevAncien::class)->findOneBy(array('id' => $prospect->getAdherent()->getOldId()));
                                        $tmp2 = $this->getTmpAdh($adh, $adh->getPecleunik(), $prospect, 1);
                                        $lastkeyArrayAdherent = $lastkeyArrayAdherent+1;
                                        $dataAdd2[$lastkeyArrayAdherent] = $tmp2; // ADD KEY INDEX OF UPDATE DATA
                                    }
                                    
                                }
                            }
                           
                        }
                    }
                    
                    //IF PERSONNE EXISTE = UPDATE
                    if($dataPers != null){
                        $personne = $em->getRepository(WindevPersonne::class)->find($dataPers->getOldId());
                        $oldId = $personne->getId();
                        $tmp = $this->getTmpPers($personne, $responsable, $oldId);
                    }
                }

                if($registered) {
                    if($dataPers == null){ // IF PERSONNE NOT EXISTE = NEW -> ADD LAST KEY INDEX 
                        $phoneMobile = $this->formatPhone($responsable->getPhoneMobile());
                        $phoneDomicile = $this->formatPhone($responsable->getPhoneDomicile());
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
        $header = array(array('ADCLEUNIK', 'PECLEUNIK', 'NUM_FICHE', 'NUM_FAMILLE', 'NOM', 'PRENOM', 'TICLEUNIK', 'NAISSANCE', 'SEXE', 'CARTEADHERENT', 
                              'TYCLEUNIK', 'INSCRIPTION', 'ADHESION', 'RENOUVELLEMENT', 'SORTIE', 'NOCOMPTA', 'CECLEUNIK', 'COMMENT', 'NOTARIF', 'DATECREATION', 
                              'DATEMAJ', 'NORAPPEL', 'LIENPROFESSEUR', 'DISPSOLFEGE', 'MTRAPPEL', 'TELEPHONE1', 'INFO_TEL1', 'TELEPHONE2', 'INFO_TEL2', 'EMAIL_ADH', 
                              'ADRESSE_ADH', 'FACTURER_ADR_PERSO', 'MRCLEUNIK', 'NB_ECH', 'BQ_DOM1', 'BQ_DOM2', 'BQ_CPTE', 'BQ_CDBEDQ', 'BQ_CDEGU', 'BQ_CLERIB', 
                              'TIRET', 'MoyenEnvoiFacture', 'MoyenEnvoiFacture_2', 'MoyenEnvoiFacture_3', 'MoyenEnvoiAbsence', 'MoyenEnvoiAbsence_2', 'MoyenEnvoiAbsence_3', 'MoyenEnvoiRelance', 'MoyenEnvoiRelance_2', 
                              'MoyenEnvoiRelance_3', 'AssoPartenaire', 'CNR_CRR', 'MajorationHM', 'IS_ANCIEN'));
        $json = $export->createFile('csv', 'ADHERENT', $fileName , $header, $data2, 54, null, 'synchro/');
        return 0;
    }

    private function getTmpPers($pers, $responsable=null, $oldId=null){
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
            $pers->getInfoTelTra(),$pers->getTelephone3(),$pers->getInfoTel3(),$pers->getTelephone4(),$pers->getInfoTel4(),$pers->getTelephone5(),$pers->getInfoTel5(),$email
        );

        return $tmp;
    }

    private function getTmpAdh($adh, $personneId, $pro=null, $isAncien=0){
        date_default_timezone_set('Europe/Paris');
        
        $id = $adh->getId();
        $numFiche = $adh->getNumFiche();
        $nom = $adh->getNom();
        $prenom = $adh->getPrenom();
        $civility = intval($adh->getTicleunik());
        $naissance = $adh->getNaissance();
        $sexe = $adh->getSexe();
        $dateMaj = $adh->getDatemaj();
        $phone1 = $adh->getTelephone1();
        $name1 = $adh->getInfoTel1();
        $phone2 = $adh->getTelephone2();
        $name2 = $adh->getInfoTel2();
        $email = $adh->getEmailAdh();
        $adr = $adh->getAdresseAdh();

        if($isAncien == 0){
            $facturer = intval($adh->getFacturerAdrPerso());
            $mr = $adh->getMrcleunik();
            $nbEch = $adh->getNbEch();
            $dom1 = $adh->getBqDom1();
            $dom2 = $adh->getBqDom2();
            $cpte = $adh->getBqCpte();
            $cdebq = $adh->getBqCdebq();
            $cdegu = $adh->getBqCdegu();
            $clerib = $adh->getBqClerib();
            $tiret = $adh->getTiret();
            $mF = intval($adh->getMoyenenvoifacture());
            $mF2 = intval($adh->getMoyenenvoifacture2());
            $mF3 = intval($adh->getMoyenenvoifacture3());
            $mA = intval($adh->getMoyenenvoiabsence());
            $mA2 = intval($adh->getMoyenenvoiabsence2());
            $mA3 = intval($adh->getMoyenenvoiabsence3());
            $mRe =  intval($adh->getMoyenenvoirelance());
            $mRe2 = intval($adh->getMoyenenvoirelance2());
            $mRe3 = intval($adh->getMoyenenvoirelance3());
            $majo = intval($adh->getMajorationhm());
        }
        

        if($pro != null){
            $nom = $pro->getLastname();
            $prenom = $pro->getFirstname();
            $civility = $this->getCivility($pro->getCivility());
            $naissance = intval(date_format($pro->getBirthday(), 'Ymd'));
            $sexe = $this->getSexe($pro->getCivility());
            $dateMaj = intval(date_format(new DateTime(), 'Ymd'));
            
            $phoneMobile = $this->formatPhone($pro->getPhoneMobile());
            $phoneDomicile = $this->formatPhone($pro->getPhoneDomicile());
            $name1 = $phoneDomicile != "" ? 'domicile' : $adh->getInfoTel1();
            $phone1 = $phoneDomicile != "" ? $phoneDomicile : $adh->getTelephone1();
            $name2 = $phoneMobile != "" ? 'mobile' : $adh->getInfoTel2();
            $phone2 = $phoneMobile != "" ? $phoneMobile : $adh->getTelephone2();

            $email = $pro->getEmail();
            $adr = $pro->getAdresseString();

            if($isAncien == 1){
                $ancien = $adh;
                $id = $ancien->getNumFiche();
                $numFiche = $ancien->getNoCompta();

                $facturer = 0; $mr = 0; $nbEch = 0;
                $dom1 = ""; $dom2 = ""; $cpte = ""; $cdebq = ""; $cdegu = ""; $clerib = ""; $tiret = "";
                $mF = 0; $mF2 = 0; $mF3 = 0; $mA = 0; $mA2 = 0; $mA3 = 0; $mRe =  0; $mRe2 = 0; $mRe3 = 0; $majo = 0;
            }
        }
        $tmp = array(
            $id, $personneId, $numFiche, $adh->getNumFamille(), $nom , $prenom, $civility, $naissance, $sexe, $adh->getCarteadherent(), 
            intval($adh->getTycleunik()), $adh->getInscription(), $adh->getAdhesion(), $adh->getRenouvellement(), $adh->getSortie(), $adh->getNocompta(), $adh->getCecleunik(), $adh->getComment(), $adh->getNotarif(), $adh->getDatecreation(), 
            $dateMaj, $adh->getNorappel(), intval($adh->getLienprofesseur()), intval($adh->getDispsolfege()), $adh->getMtrappel(), $phone1, $name1, $phone2, $name2, $email, 
            $adr, $facturer, $mr, $nbEch, $dom1, $dom2, $cpte, $cdebq, $cdegu, $clerib, $tiret, $mF, $mF2, $mF3, $mA, $mA2, $mA3, $mRe, $mRe2, $mRe3,
            intval($adh->getAssopartenaire()), intval($adh->getCnrCrr()), $majo, $isAncien
        );

        return $tmp;
    }

    private function getSexe($civ){
        return ($civ == "Mme") ? 2 : 1;
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
