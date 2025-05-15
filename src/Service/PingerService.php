<?php

namespace App\Service;

use App\Entity\Default\Computer;
use App\Entity\Ocs\Hardware;
use App\Repository\ComputerRepository;
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

class PingerService
{

   public function ping(Hardware|Computer $computer) {
      $process = new Process(['ping', '-c', '1', $computer->getIp()]);
      $process->setEnv(['LANG' => 'es_ES.UTF-8']);
      $process->setTimeout(60);
      $process->run();

      $result = $process->getOutput();
      return $result;
    }

   public function parsePingResult($result) {
      if ( null === $result) {
      }
      $lines = explode("\n", $result);
      foreach ($lines as $number => $line) {
         if ( empty($line) ) {
            continue;
         }
         if ( $number == 0 ) {
               $parsedResult = [];
               if (empty($lines[0])) {
                  return ['status' => 'Can\'t resolve name', 'datetime' => new \DateTime()];
                  continue;
               }
               $ip = mb_strcut (explode(' ', $lines[0])[2], 1, -1);
               $parsedResult = ['ip' => $ip];
         }
         if (strpos($line, 'icmp_seq=') !== false) {
               $statusString = trim(explode('icmp_seq='.$number, $line)[1]);
               if (strpos($statusString, 'Destination Host Unreachable') !== false) {
                  $parsedResult['status'] = 'unreachable';
               } else {
                  $parsedResult['status'] = 'alive';
               }
         } else if ( strpos($line, ' 100% packet loss') !== false ) {
               $parsedResult['status'] = 'unreachable';
         }
      }
      $parsedResult['datetime'] = new \DateTime();
      return $parsedResult;
   }
}