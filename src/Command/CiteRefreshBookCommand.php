<?php

namespace App\Command;

use App\Entity\TicketDay;
use App\Service\OpenDay;
use App\Service\ResponsableService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CiteRefreshBookCommand extends Command
{
    protected static $defaultName = 'cite:refresh:book';
    protected $em;
    protected $responsableService;

    public function __construct(EntityManagerInterface $entityManager, ResponsableService $responsableService)
    {
        parent::__construct();

        $this->em = $entityManager;
        $this->responsableService = $responsableService;
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $days = $this->em->getRepository(TicketDay::class)->findAll();
        foreach($days as $day){
            if($day) {
                $io->title('Delete non confirmed - ' . $day->getId());
                $this->responsableService->deleteNonConfirmed();
            }
        }
        

        $io->text('[FINISH]');

        return 0;
    }
}
