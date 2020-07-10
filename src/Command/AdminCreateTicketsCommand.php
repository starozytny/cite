<?php

namespace App\Command;

use App\Entity\TicketCreneau;
use App\Entity\TicketDay;
use App\Entity\User;
use DateTime;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AdminCreateTicketsCommand extends Command
{
    protected static $defaultName = 'admin:create:tickets';
    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->em = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Create ticket days.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Reset des tables');
        $this->resetTable($io,'ticket_day');
        $this->resetTable($io,'ticket_creneau');
        $this->resetTable($io,'ticket_responsable');
        $this->resetTable($io,'ticket_prospect');        

        $days = array(
            [
                'type' => TicketDay::TYPE_ANCIEN,
                'day' => new DateTime('2020-07-10'),
                'max' => 100,
                'remaining' => 100,
            ],
            [
                'type' => TicketDay::TYPE_ANCIEN,
                'day' => new DateTime('2020-07-11'),
                'max' => 3,
                'remaining' => 3,
            ],
            [
                'type' => TicketDay::TYPE_NOUVEAU,
                'day' => new DateTime('2020-07-12'),
                'max' => 100,
                'remaining' => 100,
            ],
            [
                'type' => TicketDay::TYPE_NOUVEAU,
                'day' => new DateTime('2020-07-13'),
                'max' => 100,
                'remaining' => 100,
            ]
        );

        $io->title('Création des journées');
        foreach ($days as $day) {
            $new = (new TicketDay())
                ->setType($day['type'])
                ->setDay($day['day'])
                ->setMax($day['max'])
                ->setRemaining($day['remaining'])
            ;

            $this->em->persist($new);

            $horaires = ['8:00', '8:15', '8:30', '8:45', '9:00'];
            foreach($horaires as $horaire){
                $s = (new TicketCreneau())
                    ->setHoraire(date_create_from_format('H:i', $horaire))
                    ->setMax(20)
                    ->setRemaining(20)
                    ->setTicketDay($new)
                ;

                $this->em->persist($s);
            }

            $io->text('Journée : ' . date_format($day['day'], 'd-m-Y') . ' créée' );
        }
        $this->em->flush();

        $io->comment('--- [FIN DE LA COMMANDE] ---');
        return 0;
    }

    protected function resetTable(SymfonyStyle $io, $item)
    {
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
            $io->error('Reset [FAIL] : ' . $e);
        }
        $io->text('Reset [OK]');
    }
}
