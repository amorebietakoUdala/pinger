<?php

namespace App\Command;

use App\Entity\Default\Vendor;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;
use League\Csv\Reader;


#[AsCommand(
    name: 'app:vendor-update',
    description: 'Add a short description for your command',
)]
class VendorUpdateCommand extends Command
{

    private string $databaseUrl='https://maclookup.app/downloads/csv-database/get-db?h=4c105062e08b98a3d75307e0e3e98a159c65f462&t=';

    public function __construct(private HttpClientInterface $httpClient, private EntityManagerInterface $em, private string $dataDir)
    {
        parent::__construct();

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
        $date = (new \DateTime())->format('y-m-d');
        $databaseUrl = $this->databaseUrl.$date;
        $io->writeln("Vendor URL Database: $databaseUrl");
        $response = $this->httpClient->request('GET', $databaseUrl);
        $headers = $response->getHeaders();
        $statusCode = $response->getStatusCode();

        if ( $statusCode === Response::HTTP_OK ) {
            if (isset($headers['content-disposition'])) {
                try {
                    $contentDisposition = $headers['content-disposition'][0];
            
                    if (preg_match('/filename="(.+?)"/', $contentDisposition, $matches)) {
                        $filename = $matches[1];
                    } else {
                        $filename = 'archivo_descargado'; 
                    }
                    $path = $this->dataDir.'/'.$filename;
                    file_put_contents($path, $response->getContent());
    
                    $query = $this->em->createQuery('DELETE FROM App\Entity\Default\Vendor');
                    $query->execute();
            
                    $csv = Reader::createFromPath($path, 'r');
                    $csv->setHeaderOffset(0); 
            
                    foreach ($csv as $row) {
                        $vendor = new Vendor();
                        $vendor->fillFromRow($row);
                        $this->em->persist($vendor);
                    }
                    $this->em->flush();
                                
                } catch (\Exception $e) {
                    $io->writeln('Error: '. $e->getMessage());
                    return Command::FAILURE;
                }
            }        
        } else {
            $io->writeln('Could\'t get vendor database');
            return Command::FAILURE;
        }
        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
