<?php

namespace App\Service;

use App\Entity\Default\Computer;
use App\Entity\Ocs\Hardware;
use Symfony\Component\Process\Process;

class NslookupService
{

    public function nslookup(string $ip): string 
    {
        $process = new Process(['nslookup', $ip]);
        $process->setTimeout(60);
        $process->run();

        return $process->getOutput();
    }

    public function parseNslookupResult(string $result): ?string 
    {
        if (empty($result)) {
            return null;
        }

        if (str_contains($result, 'server can\'t find') || str_contains($result, 'NXDOMAIN')) {
            return null;
        }

        foreach (explode("\n", $result) as $line) {
            if (str_contains($line, 'name =')) {
                $parts = explode('name =', $line);
                if (count($parts) >= 2) {
                    $name = trim(rtrim($parts[1], '. '));
                    return !empty($name) && !str_contains($name, 'server can\'t find') 
                        ? $name 
                        : null;
                }
            }
        }

        return null;
    }
}