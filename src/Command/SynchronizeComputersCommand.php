<?php

namespace App\Command;

use App\Repository\Default\ComputerRepository;
use App\Service\ActiveDirectoryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:synchronize-computers',
    description: 'Checks the ActiveDirectory for new computers and synchronizes them with the database',
)]
class SynchronizeComputersCommand extends Command
{
    public function __construct(
        private ActiveDirectoryService $ads, 
        private ComputerRepository $repo,
        private EntityManagerInterface $em,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $computers = $this->ads->getComputers();
        $counter = 0;
        
        foreach ($computers as $computer) {
            $existing = $this->repo->findOneBy(['hostname' => $computer->getHostname()]);
            if (!$existing) {
                $counter++;
                $this->em->persist($computer);
            }
        }
        $this->em->flush();
        if ($counter > 0) {
            $io->listing($computers);
            $io->success('New computers: '.$counter);
        } else {
            $io->success('No new computers found.');
        }

        return Command::SUCCESS;
    }
}
