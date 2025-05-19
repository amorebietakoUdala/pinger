<?php

namespace App\Command;

use App\Entity\Default\Computer;
use App\Repository\Default\ComputerRepository;
use App\Service\ArpService;
use App\Service\PingerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;

#[AsCommand(
    name: 'app:arp',
    description: 'arp a IP',
)]
class ArpCommand extends Command
{
    public function __construct(
        private ComputerRepository $repo,
        private EntityManagerInterface $em,
        private ArpService $arpService,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('ip', InputArgument::REQUIRED, 'IP to check');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $ip = $input->getArgument('ip');
        $io->writeln('Arp '.$ip);
        try {
            $pingResult = $this->arpService->arp($ip);
            $parsedResult = $this->arpService->parseArpResult($pingResult, $ip);
        } catch (ProcessFailedException $e) {
            $io->error('Error arp-ing '.$ip);
        } catch ( ProcessTimedOutException $e ) {
            $io->error('Timeout arp-ing '.$ip. '. Details: ' .$e->getMessage());
        }
        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }

}
