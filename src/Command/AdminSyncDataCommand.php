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

        $io->title("Données [PERSONNES - ADHERENTS]");
        $dataNouveaux = $this->getDataNouveaux();
        $dataNouveauxResponsables = $dataNouveaux[0];
        $dataNouveauxAdherents = $dataNouveaux[1];

        $io->title("Données existe [PERSONNES - ADHERENTS]");
        $dataExistes = $this->getDataExiste();
        $dataExisteResponsables = $dataExistes[0];
        $dataExisteAdherents = $dataExistes[1];

        $dataResponsables = array_merge($dataExisteResponsables, $dataNouveauxResponsables);
        $dataAdherents = array_merge($dataExisteAdherents, $dataNouveauxAdherents);

        $io->title("Création du fichier [PERSONNE]");
        $this->createFilePersonnes($dataResponsables);
        $io->title("Création du fichier [ADHERENTS]");
        $this->createFileAdherents($dataAdherents);

        $io->newLine(2);
        $io->text('------- Completed !');

        return 0;
    }

    private function getDataExiste(){
        $em = $this->em;
        $prospects = $em->getRepository(TicketProspect::class)->findBy(array('status' => self::STATUS), array('id' => 'ASC'));

        $dataResponsables = array();
        $dataAdherents = array();
        foreach ($prospects as $prospect){

            $passe = false;
            // RESPONSABLE saisie via le website
            $responsable = $prospect->getResponsable();
            $adh = null;

            if($prospect->getAdherent()){ // Si c'est un adhérent il a surement une PERSONNE
                $adh = $prospect->getAdherent();
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
                    $adh = $existe;
                    if($existe->getPersonne()){
                        $oldId = $existe->getPersonne()->getOldId();
                        $passe = true;
                    }
                }
            }

            if($passe){
                $personne = $em->getRepository(WindevPersonne::class)->find($oldId); // Get value Personne Windev
                $tmp = $this->getTmpPers($personne, $responsable, $personne->getId(), 1);
                if(!in_array($tmp, $dataResponsables)){
                    array_push($dataResponsables, $tmp);
                }

                
                if($adh->getIsAncien() == false){
                    $adherent = $em->getRepository(WindevAdherent::class)->findOneBy(array('id' => $adh->getOldId()));
                    $tmp = $this->getTmpAdh($adherent, $adherent->getPecleunik(), $prospect, 0, 1); // pas ancien et existe
                }else{
                    $adherent = $em->getRepository(WindevAncien::class)->findOneBy(array('id' => $adh->getOldId()));
                    $tmp = $this->getTmpAdh($adherent, $adherent->getPecleunik(), $prospect, 1, 1); // ancien et existe
                }

                if(!in_array($tmp, $dataAdherents)){
                    array_push($dataAdherents, $tmp);
                }

            }
        }

        return array(
            $dataResponsables,
            $dataAdherents
        );
    }

    private function getDataNouveaux(){
        $em = $this->em;
        $personnes = $em->getRepository(WindevPersonne::class)->findBy(array(), array('id' => 'ASC'));
        $adherents = $em->getRepository(CiAdherent::class)->findBy(array(), array('oldId' => 'ASC'));
        $prospects = $em->getRepository(TicketProspect::class)->findBy(array('status' => self::STATUS), array('id' => 'ASC'));

        // le dernier id de la table PERSONNE
        $lastIDResponsables = $personnes[count($personnes)-1]->getId();
        $lastIDAdherents = $adherents[count($adherents)-1]->getOldId();

        $dataNouveauxResponsables = array();
        $dataNouveauxAdherents = array();
        $noDoublonResponsables = array();
        $noDoublonAdherents = array();
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
                if(count($personnesExistent) == 0 || count($personnesExistent) > 1){ // S'il n'existe pas ou qu'il y a > 1 de résultats de PERSONNE => on créé ce nouveau PERSONNE
                    $phoneMobile = $this->formatPhone($responsable->getPhoneMobile());
                    $phoneDomicile = $this->formatPhone($responsable->getPhoneDomicile());

                    $lastIDResponsables = $lastIDResponsables + 1;

                    $tmp = array(
                        $lastIDResponsables, 0, mb_strtoupper($responsable->getLastname()), ucfirst(mb_strtolower($responsable->getFirstname())), $this->getCivility($responsable->getCivility()),
                        $responsable->getAdr(), $responsable->getComplement(), $responsable->getCp(), mb_strtoupper($responsable->getCity()),
                        $phoneMobile, $phoneMobile != "" ? 'mobile' : '', $phoneDomicile, $phoneDomicile != "" ? 'domicile' : '',
                        null,0,null,0,null,null,null,null,0,0,null,null,null,null,null,null,null,null,null,null,null,null,null,null,$responsable->getEmail(), 0
                    );

                    $tmpNoId = $tmp;
                    array_shift($tmpNoId);

                    if(!in_array($tmpNoId, $noDoublonResponsables)){
                        array_push($dataNouveauxResponsables, $tmp);
                        array_push($noDoublonResponsables, $tmpNoId);
                    }

                    // ----------- NOUVEAU ADHERENT ----------
                    $lastIDAdherents = $lastIDAdherents + 1;

                    $phone2 = $this->formatPhone($prospect->getPhoneMobile());
                    $phone1 = $this->formatPhone($prospect->getPhoneDomicile());
                    $tmp = array(
                        $lastIDAdherents,  $lastIDResponsables, null, null, mb_strtoupper($prospect->getLastname()), ucfirst(mb_strtolower($prospect->getFirstname())), $this->getCivility($prospect->getCivility()), 
                        intval(date_format($prospect->getBirthday(), 'Ymd')), $this->getSexe($prospect->getCivility()), 2, 
                        0, intval(date_format(new DateTime(), 'Ymd')), null, null, null, null, 0, null, 0, intval(date_format(new DateTime(), 'Ymd')), 
                        intval(date_format(new DateTime(), 'Ymd')), 0, 0, 0, 0, $phone1, $phone1 != "" ? 'domicile' : '', $phone2, $phone2 != "" ? 'mobile' : '', $prospect->getEmail(), 
                        $prospect->getAdresseString(), 0, 0, 0, null, null, null, null, null, null, null, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0
                    );
                    $tmpNoId = $tmp;
                    array_shift($tmpNoId);

                    if(!in_array($tmpNoId, $noDoublonAdherents)){
                        array_push($dataNouveauxAdherents, $tmp);
                        array_push($noDoublonAdherents, $tmpNoId);
                    }
                }
            }
        }

        return array(
            $dataNouveauxResponsables,
            $dataNouveauxAdherents
        );
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
            $id, intval($pers->getTycleunik()), mb_strtoupper($lastname), ucfirst(mb_strtolower($firstname)), $civility, $adr, $complement, $cp, $city, $phone1, 
            $name_phone1 , $phone2, $name_phone2, $pers->getNocompta(),intval($pers->getSfcleunik()),$pers->getNaissance(),intval($pers->getCacleunik()),$pers->getProfession(), $pers->getAdresseTrav(),$pers->getTelTrav(),
            $pers->getComment(),$pers->getMrcleunik(),$pers->getNbEch(), $pers->getBqDom1(),$pers->getBqDom2(),$pers->getBqCpte(),$pers->getBqCdebq(),$pers->getBqCdegu(),$pers->getBqClerib(),$pers->getTiret(),
            $pers->getInfoTelTra(),$pers->getTelephone3(),$pers->getInfoTel3(),$pers->getTelephone4(),$pers->getInfoTel4(),$pers->getTelephone5(),$pers->getInfoTel5(),$email, $isExsite
        );

        return $tmp;
    }

    private function getTmpAdh($adh, $personneId, $pro=null, $isAncien=0, $isExsite=0){
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
            $id, $personneId, $numFiche, $adh->getNumFamille(), mb_strtoupper($nom), ucfirst(mb_strtolower($prenom)), $civility, $naissance, $sexe, $adh->getCarteadherent(), 
            intval($adh->getTycleunik()), $adh->getInscription(), $adh->getAdhesion(), $adh->getRenouvellement(), $adh->getSortie(), $adh->getNocompta(), $adh->getCecleunik(), $adh->getComment(), $adh->getNotarif(), $adh->getDatecreation(), 
            $dateMaj, $adh->getNorappel(), intval($adh->getLienprofesseur()), intval($adh->getDispsolfege()), intval($adh->getMtrappel()), $phone1, $name1, $phone2, $name2, $email, 
            $adr, $facturer, $mr, $nbEch, $dom1, $dom2, $cpte, $cdebq, $cdegu, $clerib, $tiret, $mF, $mF2, $mF3, $mA, $mA2, $mA3, $mRe, $mRe2, $mRe3,
            intval($adh->getAssopartenaire()), intval($adh->getCnrCrr()), $majo, $isAncien, $isExsite
        );

        return $tmp;
    }

    private function createFileAdherents($data)
    {
        $header = array(array('ADCLEUNIK', 'PECLEUNIK', 'NUM_FICHE', 'NUM_FAMILLE', 'NOM', 'PRENOM', 'TICLEUNIK', 'NAISSANCE', 'SEXE', 'CARTEADHERENT', 
        'TYCLEUNIK', 'INSCRIPTION', 'ADHESION', 'RENOUVELLEMENT', 'SORTIE', 'NOCOMPTA', 'CECLEUNIK', 'COMMENT', 'NOTARIF', 'DATECREATION', 
        'DATEMAJ', 'NORAPPEL', 'LIENPROFESSEUR', 'DISPSOLFEGE', 'MTRAPPEL', 'TELEPHONE1', 'INFO_TEL1', 'TELEPHONE2', 'INFO_TEL2', 'EMAIL_ADH', 
        'ADRESSE_ADH', 'FACTURER_ADR_PERSO', 'MRCLEUNIK', 'NB_ECH', 'BQ_DOM1', 'BQ_DOM2', 'BQ_CPTE', 'BQ_CDEBQ', 'BQ_CDEGU', 'BQ_CLERIB', 
        'TIRET', 'MoyenEnvoiFacture', 'MoyenEnvoiFacture_2', 'MoyenEnvoiFacture_3', 'MoyenEnvoiAbsence', 'MoyenEnvoiAbsence_2', 'MoyenEnvoiAbsence_3', 'MoyenEnvoiRelance', 'MoyenEnvoiRelance_2', 
        'MoyenEnvoiRelance_3', 'AssoPartenaire', 'CNR_CRR', 'MajorationHM', 'IS_ANCIEN', 'IS_EXISTE'));
        $json = $this->export->createFile('csv', 'ADHERENT', 'ADHERENT.csv' , $header, $data, 55, null, 'synchro/');
    }

    private function createFilePersonnes($data)
    {
        $header = array(array('PECLEUNIK', 'TYCLEUNIK', 'NOM', 'PRENOM', 'TICLEUNIK', 'ADRESSE1', 'ADRESSE2', 'CDE_POSTAL', 'VILLE', 'TELEPHONE1', 'INFO_TEL1', 'TELEPHONE2', 'INFO_TEL2',
        'NOCOMPTA', 'SFCLEUNIK', 'NAISSANCE', 'CACLEUNIK', 'PROFESSION', 'ADRESSE_TRAV', 'TEL_TRAV', 'COMMENT', 'MRCLEUNIK', 'NB_ECH', 'BQ_DOM1', 'BQ_DOM2',
        'BQ_CPTE', 'BQ_CDEBQ', 'BQ_CDEGU', 'BQ_CLERIB', 'TIRET', 'INFO_TEL_TRA', 'TELEPHONE3', 'INFO_TEL3', 'TELEPHONE4', 'INFO_TEL4', 'TELEPHONE5', 'INFO_TEL5', 'EMAIL_PERS', 'IS_EXISTE'));
        $json = $this->export->createFile('csv', 'PERSONNE', 'PERSONNE.csv' , $header, $data, 39, null, 'synchro/');
    }

    private function getSexe($civ){
        return ($civ == "Mme") ? 2 : 1;
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
