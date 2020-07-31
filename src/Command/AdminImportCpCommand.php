<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AdminImportCpCommand extends Command
{
    protected static $defaultName = 'admin:import:cp';

    protected function configure()
    {
        $this
            ->setDescription('Créer l\'entity passé en argument.')
            ->addArgument('nameTable', InputArgument::REQUIRED, 'nom de la table')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $nameTable = $input->getArgument('nameTable');

        $command = $this->getApplication()->find('do:mapping:import');
        $arguments = [
            'command' => 'do:mapping:import',
            'name' => 'App\Entity\Data',
            'mapping-type' => 'annotation',
            '--path' => 'src/Entity/Data',
            '--filter' => $nameTable
        ];
        $greetInput = new ArrayInput($arguments);
        try {
            $command->run($greetInput, $output);
        } catch (\Exception $e) {
            $io->error('Erreur run do:ma:import : ' . $e);
        }

        $io->warning('run command update doctrine !');

        return 0;
    }
}
