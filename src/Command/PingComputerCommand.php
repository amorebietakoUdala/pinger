<?php

namespace App\Command;

use App\Entity\Default\Computer;
use App\Entity\Ocs\Hardware;
use App\Entity\Ocs\Subnet;
use App\Repository\Default\ComputerRepository;
use App\Repository\Ocs\HardwareRepository;
use App\Repository\Ocs\SubnetRepository;
use App\Service\ArpService;
use App\Service\NslookupService;
use App\ServiceslookupService;
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
    name: 'app:ping-computer',
    description: 'Pings the computer specified by de ip and updates it\'s status',
)]
class PingComputerCommand extends Command
{
    public function __construct(
        private ComputerRepository $computerRepo,
        private HardwareRepository $hardwareRepo,
        private SubnetRepository $subnetRepo,
        private EntityManagerInterface $em,
        private PingerService $pingerService,
        private ArpService $arpService,
        private NslookupService $nslookupService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('ip', InputArgument::OPTIONAL, 'Specifies which computer name to start pinging');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $ipString = $input->getArgument('ip');
        $result = $this->nslookupService->nslookup($ipString);
        $parseresult = $this->nslookupService->parseNslookupResult($result);

        $computer = $this->computerRepo->findOneBy([
            'ip' => $ipString
        ]);
        if (null === $computer) {
            $computer = new Computer();
            $computer->setIp($ipString);
            $computer->setOrigin('Pinger');
        }
        $cleanedHostname = '{unknown}';
        if ($parseresult != null) {
            
            $cleanedHostname = str_replace([".udala.local", ".amorebieta-etxano.eus"], "", $parseresult);

            if (null !== $cleanedHostname) {
                $computer->setHostname($cleanedHostname);
            }
        }
        $hardware = $this->hardwareRepo->findOneBy(['ipaddr' => $ipString]);
        if (null !== $hardware) {
            if ( null === $computer->getHostname()) {
                $computer->setHostname($hardware->getName());
            }
            $computer->setLastInventory($hardware->getLastdate());
            $computer->setLastOcsContact($hardware->getLastcome());
            $computer->setHardwareId($hardware->getId());
            $computer->setOrigin('OCS');
        }
        try {
            $pingResult = $this->pingerService->ping($computer);
            $parsedResult = $this->pingerService->parsePingResult($pingResult);

            if (array_key_exists('ip', $parsedResult) && $parsedResult['ip'] != null) {
                $computer->setIp($parsedResult['ip'] ?? null);
                $arpResult = $this->arpService->arp($parsedResult['ip']);
                $arpParsedResult = $this->arpService->parseArpResult($arpResult, $parsedResult['ip']);
                if ($arpParsedResult['mac'] !== null) {
                    $computer->setMac($arpParsedResult['mac']);
                } else {
                    if ($hardware !== null) {
                        $network = $hardware->getFirstUpNetwork();
                        if ($network !== null) {
                            $computer->setMac(mb_strtolower($network->getMacAddr()));
                        }
                    }
                }
                if (array_key_exists('status', $parsedResult) && $parsedResult['status'] == 'alive') {
                    $computer->setLastSucessfullPing($parsedResult['datetime']);
                    $pingres = true;
                } else {
                    $pingres = false;
                }
            }
        } catch (ProcessFailedException $e) {
            $io->error('Error pinging ' . $computer->getHostname());
        } catch (ProcessTimedOutException $e) {
            $io->error('Timeout pinging ' . $computer->getHostname() . '. Details: ' . $e->getMessage());
        }

        $this->em->persist($computer);
        $this->em->flush();
        if ($pingres) {
            $io->writeln("$ipString-$cleanedHostname-Ping successfull");
        } else {
            $io->writeln("$ipString-$cleanedHostname-Ping unsuccessfull");
        }
        return Command::SUCCESS;
    }
}
