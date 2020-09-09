<?php

namespace App\Command;

use App\Entity\TicketProspect;
use App\Entity\TicketResponsable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AdminCleanDataCommand extends Command
{
    protected static $defaultName = 'admin:clean:data';
    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->em = $entityManager;
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
        $em = $this->em;

        $prospects = $em->getRepository(TicketProspect::class)->findBy(['status' => TicketProspect::ST_CONFIRMED], ['lastname' => 'ASC']);

        foreach($prospects as $p){
            $p->setLastname($this->setUpper($p->getLastname()));
            $p->setFirstname($this->setCapitalize($p->getFirstname()));
            $p->setAdr($this->setUpper($p->getAdr()));
            $p->setCity($this->setUpper($p->getCity()));
            $p->setPhoneMobile($this->removeSpace($p->getPhoneMobile()));
            $p->setPhoneDomicile($this->removeSpace($p->getPhoneDomicile()));

            $em->persist($p);
        }

        $responsables = $em->getRepository(TicketResponsable::class)->findBy(['status' => TicketResponsable::ST_CONFIRMED]);

        foreach($responsables as $r){
            $r->setLastname($this->setUpper($r->getLastname()));
            $r->setFirstname($this->setCapitalize($r->getFirstname()));
            $r->setAdr($this->setUpper($r->getAdr()));
            $r->setCity($this->setUpper($r->getCity()));
            $r->setPhoneMobile($this->removeSpace($r->getPhoneMobile()));
            $r->setPhoneDomicile($this->removeSpace($r->getPhoneDomicile()));

            $em->persist($r);
        }

        $em->flush();

        $io->success('Clean.');

        return 0;
    }

    private function removeSpace($string){
        $string = str_replace(
            array(' ', '.'),
            array('', ''),
            $string
         );
         return $string;
    }

    private function deleteAccent($string){
        $string = str_replace(
           array('é', 'è', 'ê', 'ë', 'à', 'â', 'î', 'ï', 'ô', 'ù', 'û', 'É', 'È', 'Ê', 'Ë', 'À', 'Â', 'Î', 'Ï', 'Ô', 'Ù', 'Û', 'ç','Ç'),
           array('e','e','e','e','a','a','i','i','o','u','u','E','E','E','E','A','A','I','I','O','U','U', 'c', 'C'),
           $string
        );
        return $string;
    }

    private function setCapitalize($string){
        return ucfirst(mb_strtolower($this->deleteAccent(trim($string))));
    }

    private function setUpper($string){
        return mb_strtoupper($this->deleteAccent(trim($string)));
    }
}
