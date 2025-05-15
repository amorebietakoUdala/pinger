<?php

namespace App\Command;

use App\Repository\Default\ComputerRepository;
use App\Service\NslookupService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;

#[AsCommand(
    name: 'app:nslookup',
    description: 'Perform nslookup for a given IP address',
)]
class NslookupComputersCommand extends Command
{
    public function __construct(
        private ComputerRepository $repo,
        private EntityManagerInterface $em,
        private NslookupService $nslookupService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('ip', InputArgument::REQUIRED, 'IP address to check');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $ip = $input->getArgument('ip');
        
        $io->title("Performing nslookup for IP: $ip");
        
        try {

            $result = $this->nslookupService->nslookup($ip);
            
            $hostname = $this->nslookupService->parseNslookupResult($result);
            
            if ($hostname) {
                $io->success("Hostname found: $hostname");
            } else {
                $io->warning("No hostname found for IP: $ip");
            }
            
            return Command::SUCCESS;
            
        } catch (ProcessFailedException $e) {
            $io->error("nslookup failed for IP: $ip - " . $e->getMessage());
            return Command::FAILURE;
            
        } catch (ProcessTimedOutException $e) {
            $io->error("nslookup timed out for IP: $ip - " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}