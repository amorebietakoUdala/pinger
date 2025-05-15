<?php

namespace App\Command;

use App\Entity\Default\Computer;
use App\Entity\Ocs\Hardware;
use App\Repository\Default\ComputerRepository;
use App\Repository\Ocs\HardwareRepository;
use App\Repository\Ocs\SubnetRepository;
use App\Service\ArpService;
use App\Service\NslookupService;
use App\Service\PingerService;
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
    name: 'app:ping-computers',
    description: 'Ping all the computers on the Ocs database. It synchronizes with pinger database if there are new computers on Ocs',
)]
class PingComputersCommand extends Command
{
    public function __construct(
        private ComputerRepository $computerRepo,
        private HardwareRepository $hardwareRepo,
        private SubnetRepository $subnetRepo,
        private EntityManagerInterface $em,
        private PingerService $pingerService,
        private ArpService $arpService,
        private NslookupService $nslookupService,
        private array $excludedSubnets,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('start', InputArgument::OPTIONAL, 'Specifies which computer name to start pinging');
        $this->addArgument('end', InputArgument::OPTIONAL, 'Specifies which computer name where to stop pinging');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $startIp = $input->getArgument('start');
        $endIp = $input->getArgument('end');

        $startLong = $startIp ? ip2long($startIp) : null;
        $endLong = $endIp ? ip2long($endIp) : null;

        //$subnets = $this->subnetRepo->findByTag('{"scan":"true"}');

        $subnets = $this->subnetRepo->findAll();

        foreach ($subnets as $net) {
            if ($this->excludedSubnet($net->getNetId())) {
                $io->note("Excluding subnet: {$net->getNetId()}");
                continue;
            }
            $this->processSubnet($net, $io, $startLong, $endLong);
        }

        return Command::SUCCESS;
    }


    private function processSubnet($net, SymfonyStyle $io, ?int $startLong, ?int $endLong): void
    {
        $netId = $net->getNetId();
        $mask = $net->getMask();

        $networkIp = ip2long($netId);
        $subnetMask = ip2long($mask);

        if ($networkIp === false || $subnetMask === false) {
            $io->writeln("Error con la IP o máscara: {$netId} / {$mask}");
            return;
        }

        $broadcastIp = ($networkIp | (~$subnetMask & 0xFFFFFFFF));
        $hasIpInRange = false;

        for ($ip = $networkIp + 1; $ip < $broadcastIp; $ip++) {
            if (($startLong && $ip < $startLong) || ($endLong && $ip > $endLong)) {
                continue;
            }
            if ($this->excludedSubnet($ip)) {
                continue;
            }

            $hasIpInRange = true;
            break;
        }

        if (!$hasIpInRange) {
            return;
        }

        $io->writeln("Subred: {$net->getName()} ({$netId} / {$mask})");
        $io->writeln("------------------------------------");

        for ($ip = $networkIp + 1; $ip < $broadcastIp; $ip++) {
            if (($startLong && $ip < $startLong) || ($endLong && $ip > $endLong)) {
                continue;
            }

            $ipString = long2ip($ip);
            if (strpos($ipString, "172.") === 0 || strpos($ipString, "127.") === 0) {
                continue;
            }

            $this->processIp($ipString, $io);
        }

        $io->writeln("------------------------------------");
    }

    private function processIp(string $ipString, SymfonyStyle $io): void
    {
        try {
            $cleanedHostname = null;
            $hardware = $this->hardwareRepo->findOneByIp($ipString);
            if ( null !== $hardware) {
                $cleanedHostname = $hardware->getName();
                $from = 'OCS';
            } else {
                $result = $this->nslookupService->nslookup($ipString);
                $parseresult = $this->nslookupService->parseNslookupResult($result);
                $cleanedHostname = $parseresult ? str_replace([".udala.local", ".amorebieta-etxano.eus"], "", $parseresult) : null;
                $from = 'DNS';
            }
            $computer = $this->findOrCreateComputer($ipString, $cleanedHostname, $hardware);
            $this->updateWithHardwareData($computer, $hardware);

            $pingResult = $this->pingerService->ping($computer);
            $parsedResult = $this->pingerService->parsePingResult($pingResult);

            if (!isset($parsedResult['ip']) || $parsedResult['ip'] === null || $parsedResult['status'] !== 'alive') {
                $io->writeln("$ipString-$cleanedHostname-($from)-Ping unsuccessfull");
                return;
            }

            $computer->setIp($parsedResult['ip']);
            $this->handleArpData($computer, $parsedResult['ip']);
            $computer->setLastSucessfullPing($parsedResult['datetime']);

            $this->em->persist($computer);
            $this->em->flush();

            $io->writeln("$ipString-$cleanedHostname-($from)-Ping successfull");
        } catch (ProcessFailedException | ProcessTimedOutException $e) {
            $io->error('Error pinging ' . ($computer->getHostname() ?? $ipString) . ': ' . $e->getMessage());
        }
    }

    private function findOrCreateComputer(string $ip, ?string $hostname, ?Hardware $hardware): Computer
    {
        if ( null !== $hostname) {
            $computer = $this->computerRepo->findOneBy(['hostname' => $hostname]);
            if ( null !== $computer){
                $computer->setIp($ip);
            }
        } else {
            $computer = $this->computerRepo->findOneBy(['ip' => $ip]);
        }
        if (!$computer) {
            $computer = new Computer();
            $computer->setIp($ip);
            $computer->setOrigin('Pinger');
        }

        if ($hostname) {
            $computer->setHostname($hostname);
        }

        return $computer;
    }

    private function updateWithHardwareData(Computer &$computer, ?Hardware $hardware): ?Hardware
{
    if (null !== $hardware) {
        if (!$computer->getHostname()) {
            $computer->setHostname($hardware->getName());
        }
        $computer->setLastInventory($hardware->getLastdate());
        $computer->setLastOcsContact($hardware->getLastcome());
        $computer->setHardwareId($hardware->getId());
        $computer->setOrigin('OCS');
    }
    
    return $hardware; // Puede ser null
}
    private function handleArpData(Computer $computer, string $ip): void
    {
        $arpResult = $this->arpService->arp($ip);
        $arpParsed = $this->arpService->parseArpResult($arpResult, $ip);
        if ($arpParsed['mac'] ?? null) {
            $computer->setMac($arpParsed['mac']);
        } else {
            $hardware = $this->hardwareRepo->findOneBy(['ipaddr' => $ip]);
            if ($hardware && $network = $hardware->getFirstUpNetwork()) {
                $computer->setMac(mb_strtolower($network->getMacAddr()));
            }
        }
    }

    private function excludedSubnet($ipString): bool
    {
        foreach ($this->excludedSubnets as $subnet) {
            if (strpos($ipString, $subnet) === 0) {
                return true;
            }
        }
        return false;
    }
}
