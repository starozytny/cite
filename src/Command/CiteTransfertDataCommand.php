<?php

namespace App\Command;

use App\Entity\Windev\WindevAdherent;
use App\Entity\Windev\WindevAncien;
use App\Entity\Windev\WindevPersonne;
use App\Manager\Transfert;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CiteTransfertDataCommand extends Command
{
    protected static $defaultName = 'cite:transfert:data';
    private $em;
    private $transfert;

    public function __construct(EntityManagerInterface $em, Transfert $transfert)
    {
        parent::__construct();
        $this->em = $em;
        $this->transfert = $transfert;
    }

    protected function configure()
    {
        $this
            ->setDescription('Transfert data windev to data website')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $em = $this->em;

        $io->title('Reset des tables');
        $this->resetTable($io);

        $io->text('Création et remplissage [OK]');

        $this->transfertTable($io, 'windev_personne', WindevPersonne::class);
        $this->transfertTable($io, 'windev_adherent', WindevAdherent::class);
        $this->transfertTable($io, 'windev_ancien', WindevAncien::class);

//        $io->title('Drop des tables windev');
//        $this->dropTable($io);

        $io->comment('FIN [OK]');
        return 0;
    }

    /**
     * @param $table
     * @param $item
     */
    protected function fillTable($table, $item)
    {
        $obj = null; $filled = true;
        switch ($table){
            case 'windev_personne':
                $obj = $this->transfert->createPersonne($item);
                break;
            case 'windev_adherent':
                $obj = $this->transfert->createAdherent($item, false);
                break;
            case 'windev_ancien':
                $obj = $this->transfert->createAdherent($item, true);
                break;
            default:
                break;
        }
        if($filled){
            $this->em->persist($obj);
        }
    }

    protected function transfertTable(SymfonyStyle $io, $table, $classe)
    {
        $schema = $this->em->getConnection()->getSchemaManager();
        if($schema->tablesExist(array($table))){
            $io->title('Début transfert ' . $table . ' : ');
            $tmp = $this->em->getRepository($classe)->findAll();
            
            if($tmp){
                $compteur = 0;

                foreach ($tmp as $item) {
                    $this->fillTable($table, $item);
                    $compteur++;
                }
                $this->em->flush();
                $io->text($compteur . ' données transférées.');
            }else{
                $io->comment('Aucune donnée dans la table ' . $table);
            }
        }else{
            $io->comment('Aucune table - ' . $table);
        }
    }

    protected function resetTable(SymfonyStyle $io){
        $list = array(
            'ci_personne',
            'ci_adherent'
        );
        foreach ($list as $item) {
            $connection = $this->em->getConnection();

            $connection->beginTransaction();
            try {
                $connection->query('SET FOREIGN_KEY_CHECKS=0');
                $connection->executeUpdate(
                    $connection->getDatabasePlatform()->getTruncateTableSQL(
                        $item, true
                    )
                );
                $connection->query('SET FOREIGN_KEY_CHECKS=1');
                $connection->commit();
            } catch (DBALException $e) {
                $io->error('Database reset [FAIL] : ' . $e);
            }

        }
        $io->text('Reset [OK]');
    }

    protected function dropTable(SymfonyStyle $io){
        $list = array(
            'windev_ancien',
            'windev_adherent',
            'windev_personne'
        );
        foreach ($list as $item) {
            $connection = $this->em->getConnection();

            $connection->beginTransaction();
            try {
                $connection->query('SET FOREIGN_KEY_CHECKS=0');
                $connection->executeUpdate(
                    $connection->getDatabasePlatform()->getDropTableSQL($item)
                );
                $connection->query('SET FOREIGN_KEY_CHECKS=1');
                $connection->commit();
            } catch (DBALException $e) {
                $io->error('Database Drop [FAIL] : ' . $e);
            }

        }
        $io->text('Drop [OK]');
    }
}
