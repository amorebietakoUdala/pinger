<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use UniFi_API\Client;
use App\Entity\Default\UnifiAccessPoint;
use App\Repository\Default\UnifiAccessPointRepository;
use App\Service\PingerService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:ping-unifi-access-points',
    description: 'Pings Unifi Access Points and updates their status in the database.',
)]
class UnifiAccessPointPingerCommand extends Command
{

    private Client $unifiConnection;

    public function __construct(
        private $unifiControllerDebug, 
        private $unifiControllerUser, 
        private $unifiControllerPassword, 
        private $unifiControllerUrl, 
        private $unifiControllerSiteId, 
        private $unifiControllerVersion,
        private UnifiAccessPointRepository $repo,
        private EntityManagerInterface $em,
        private PingerService $pingerService,
    )
    {
        parent::__construct();
        $this->unifiConnection = new Client(
            $this->unifiControllerUser,
            $this->unifiControllerPassword,
            $this->unifiControllerUrl,
            $this->unifiControllerSiteId,
            $this->unifiControllerVersion
        );

    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->success('Access Point Pinger started...');
        $loginresults   = $this->unifiConnection->login();
        $devices_array      = $this->unifiConnection->list_devices();
        $io->writeln('Devices found: ' . count($devices_array));

        foreach ($devices_array as $device) {
            $device = json_decode(json_encode($device), true);
            $mac = $device['mac'];
            $ip = $device['ip'];
            $name = $device['name'] ?? 'Unknown';
            $state = $device['state'] ?? 'Unknown';
            $disabled = $device['disabled'] ?? false;
            // $start_connected_millis = $device['start_connected_millis'];
            
            // $startConnectedDate('Y-m-d H:i:s', $start_connected_millis);

            // Check if the access point already exists in the database
            /** @var UnifiAccessPoint $accessPoint */  
            $accessPoint = $this->repo->findOneBy(['mac' => $mac]);

            if (!$accessPoint) {
                // Create a new UnifiAccessPoint entity
                $accessPoint = new UnifiAccessPoint();
                $accessPoint->setMac($mac);
                $accessPoint->setIp($ip);
                $accessPoint->setName($name);
            }

            // Update the status and other fields
            $accessPoint->setState(UnifiAccessPoint::STATES[$state] ?? $state);
            $accessPoint->setDisabled($disabled);
            if ( $state === 1 ) {
                $accessPoint->setLastTimeOnline(new \DateTime());
            }
            // Add any other fields you want to update
            $result = $this->pingerService->ping($accessPoint->getIp());
            $parsedResult = $this->pingerService->parsePingResult($result);
            $datetime = new \DateTime();
            $datetimeString = $datetime->format('Y-m-d H:i:s');
            $lastTimeOnLineString = $accessPoint->getLastTimeOnline() !== null ? $accessPoint->getLastTimeOnline()->format('Y-m-d H:i:s') : '';
            if (!isset($parsedResult['ip']) || $parsedResult['ip'] === null || $parsedResult['status'] !== 'alive') {
                $io->writeln($datetimeString.' - '.$accessPoint->getIp() . ' - mac: ' . $accessPoint->getMac() . ' - name: ' . $accessPoint->getName() . ' - state: ' . $accessPoint->getState() . ' - ping: Unsuccessful - LastTimeOnLine: '. $lastTimeOnLineString);
                $accessPoint->setPingStatus('unreachable');
            } else {
                $io->writeln($datetimeString.' - '. $accessPoint->getIp() . ' - mac: ' . $accessPoint->getMac() . ' - name: ' . $accessPoint->getName() . ' - state: ' . $accessPoint->getState() . ' - ping: Successful - LastTimeOnLine: '. $lastTimeOnLineString);
                $accessPoint->setPingStatus('alive');
                $accessPoint->setLastSuccessfullPing(new \DateTime());
            }
            // Persist the changes
            $this->em->persist($accessPoint);
            $this->em->flush();
        }

        $io->success('Access Point Pinger ended succesfully.');

        return Command::SUCCESS;
    }
}
