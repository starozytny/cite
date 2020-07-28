<?php

namespace App\Command;

use App\Entity\Cite\CiPersonne;
use App\Entity\TicketResponsable;
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
        $personnes = $em->getRepository(CiPersonne::class)->findBy(array(), array('oldId' => 'DESC'));
        
        $data = array();
        $dataAdd = array();
        $constOldId = $personnes[0]->getOldId();


        $io->title("Début de la synchronisation");
        $progressBar = new ProgressBar($output, count($personnes) + count($responsables));
        $progressBar->setFormat("%current%/%max% [%bar%] %percent:3s%%  🏁");
        $progressBar->setOverwrite(true);
        $progressBar->start();

        foreach ($personnes as $pers){
            $progressBar->advance();
            $tmp = array(
                $pers->getOldId(), 0, $pers->getLastname(), $pers->getFirstname(), $this->getCivility($pers->getCivility()),
                $pers->getAdr(), $pers->getComplement(), $pers->getCp(), $pers->getCity(),
                $pers->getPhoneMobile(), 'mobile', $pers->getPhoneDomicile(), 'domicile',
                null,0,null,0,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,$pers->getEmail()
            );

            if(!in_array($tmp, $data)){
                array_push($data, $tmp);
            }   
        }

        foreach ($responsables as $responsable) {
            // $progressBar->advance();
            $prospects = $responsable->getProspects();
            if(count($prospects) > 0 && $responsable->getStatus() != TicketResponsable::ST_TMP){

                $phoneMobile = $responsable->getPhoneMobile();
                $phoneDomicile = $responsable->getPhoneDomicile();

                $dataPers = null;
                if($prospects[0]->getAdherent()){
                    $dataPers = $prospects[0]->getAdherent()->getPersonne();
                    if(count($prospects) > 1){
                        foreach ($prospects as $prospect){
                            if($prospect->getAdherent()){
                                if($dataPers != $prospect->getAdherent()->getPersonne()){
                                    $dataPers = null;
                                }
                            }
                        }
                    }
                    
                    if($dataPers != null){
                        $phoneMobile = $phoneMobile != "" ? $phoneMobile : $dataPers->getPhoneMobile();
                        $phoneDomicile = $phoneDomicile != "" ? $phoneDomicile : $dataPers->getPhoneDomicile();
                        $oldId = $dataPers->getOldId();
                    }else{
                        $constOldId = $constOldId + 1;
                        $oldId = $constOldId;
                    }
                }

                $tmp = array(
                    $oldId, 0, $responsable->getLastname(), $responsable->getFirstname(), $this->getCivility($responsable->getCivility()),
                    $responsable->getAdr(), $responsable->getComplement(), $responsable->getCp(), $responsable->getCity(),
                    $phoneMobile, 'mobile', $phoneDomicile, 'domicile',
                    null,0,null,0,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,$responsable->getEmail()
                );
                
                if($dataPers == null){
                    if(!in_array($tmp, $dataAdd)){
                        array_push($dataAdd, $tmp);
                    }
                }else{
                    $dataAdd[array_search($oldId, array_column($data, 0))] = $tmp;
                }
            }
        }

        $data = array_replace($data, $dataAdd);

        $progressBar->finish();
        $io->text('------- Completed !');

        $fileName = 'PERSONNE.csv';

        $header = array(array('PECLEUNIK', 'TYCLEUNIK', 'NOM', 'PRENOM', 'TICLEUNIK', 'ADRESSE1', 'ADRESSE2', 'CDE_POSTAL', 'VILLE', 'TELEPHONE1', 'INFO_TEL1', 'TELEPHONE2', 'INFO_TEL2',
                              'NOCOMPTA', 'SFCLEUNIK', 'NAISSANCE', 'CACLEUNIK', 'PROFESSION', 'ADRESSE_TRAV', 'TEL_TRAV', 'COMMENT', 'MRCLEUNIK', 'NB_ECH', 'BQ_DOM1', 'BQ_DOM2',
                              'BQ_CPTE', 'BQ_CDBEDQ', 'BQ_CDEGU', 'BQ_CLERIB', 'TIRET', 'INFO_TEL_TRA', 'TELEPHONE3', 'INFO_TEL3', 'TELEPHONE4', 'INFO_TEL4', 'TELEPHONE5', 'INFO_TEL5', 'EMAIL_PERS'));
        $json = $export->createFile('csv', 'PERSONNE', $fileName , $header, $data, 38, null, 'synchro/');

        return 0;
    }
}
