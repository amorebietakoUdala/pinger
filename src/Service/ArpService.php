<?php

namespace App\Service;

use Symfony\Component\Process\Process;


class ArpService
{

   public function arp(string $ip): string {
      $process = new Process(['arp', '-v', $ip]);
      $process->setEnv(['LANG' => 'es_ES.UTF-8']);
      $process->setTimeout(60);
      $process->run();

      $result = $process->getOutput();
      return $result;
    }

   public function parseArpResult($result, $ip) {
      if ( null === $result) {
      }
      $tokenizedLines = [];
      $result = str_replace('Indic Máscara','Indic_Máscara',$result);
      $lines = explode("\n", $result);
      foreach ($lines as $number => $line) {
         $tokenizedLine = explode(' ', $this->reduceSpaces($line));
         $tokenizedLines[] = $tokenizedLine;
         if ( $number == 0 ) {
               $parsedResult = [];
         } else {
            if (strpos($line, 'no hay entradas') !== false) {
               return ['status' => 'Not Found', 'datetime' => new \DateTime(), 'ip' => $ip, 'mac' => null];
               continue;
            }
            $parsedResult['mac'] = $this->getField($tokenizedLines, 'DirecciónHW');
            $parsedResult['hostname'] = $this->getField($tokenizedLines, 'Dirección');
            $parsedResult['datetime'] = new \DateTime();
         }
      }
      $parsedResult['ip'] = $ip;
      return $parsedResult;
   }

   function reduceSpaces($string) {
      return preg_replace('/\s+/', ' ', trim($string));
   }

   private function getField($tokenizedLines, $field) {
      foreach ($tokenizedLines as $row => $tokenizedLine) {
         foreach ( $tokenizedLine as $key => $value ) {
            if ( $value === $field ) {
               if (count($tokenizedLines[$row]) === count($tokenizedLines[$row+1]) ) {
                  return $tokenizedLines[$row+1][$key];
               }
            }
         }
      }
      return null;
   }
}