<?php

namespace App\Controller;

use App\Entity\Ocs\Subnet;
use App\Repository\Default\ComputerRepository;
use App\Repository\Ocs\SubnetRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/{_locale}')]
final class SubnetController extends BaseController
{
    public function __construct(private SubnetRepository $repo, private ComputerRepository $compRepo) {}

    #[Route('/subnet', name: 'subnet_index')]
    public function index(Request $request): Response
    {
        $this->loadQueryParameters($request);
        $this->queryParams['pageSize'] = 100;
        $subnets = $this->repo->findAll();
        $counters = [];
        foreach ($subnets as $subnet) {
            $computers = $this->compRepo->findbetweenStartAndEndip(
                $subnet->getFirstUsableIp(),
                $subnet->getLastUsableIp()
            );
            $counters[$subnet->getPk()] = count($computers);
            $noHostnameCount = 0;
            foreach ($computers as $computer) {
                if (empty($computer->getHostname())) {
                    $noHostnameCount++;
                }
            }
            $noHostnameCounters[$subnet->getPk()] = $noHostnameCount;
        }

        $template = $request->isXmlHttpRequest() ? '_list.html.twig' : 'index.html.twig';

        return $this->render('subnet/' . $template, [
            'subnets' => $subnets,
            'counters' => $counters,
            'noHostnameCounters' => $noHostnameCounters,

        ]);
    }

    #[Route('/subnet/{subnet}/free-ips', name: 'subnet_free_ips_index')]
    public function freeIps(Request $request, Subnet $subnet): Response
    {
        $this->loadQueryParameters($request);
        $this->queryParams['pageSize'] = 100;

        $ips = $this->getSubnetIpList($subnet);
        $computers = $this->compRepo->findbetweenStartAndEndip($subnet->getFirstUsableIp(), $subnet->getLastUsableIp());
        foreach ($computers as $computer) {
            $ip = ip2long($computer->getIp());
            if ((array_key_exists($ip, $ips)) !== false) {
                unset($ips[$ip]);
            }
        }
        $template = $request->isXmlHttpRequest() ? '_list.html.twig' : 'free_ips.html.twig';

        return $this->render('subnet/' . $template, [
            'ips' => $ips,
        ]);
    }

    private function getSubnetIpList(Subnet $subnet): array
    {
        $networkIp = ip2long($subnet->getNetId());
        $subnetMask = ip2long($subnet->getMask());
        $broadcastIp = ($networkIp | (~$subnetMask & 0xFFFFFFFF));

        $ips = [];
        for ($ip = $networkIp + 1; $ip < $broadcastIp; $ip++) {
            $ipString = long2ip($ip);
            $ips[$ip] = $ipString;
        }
        return $ips;
    }
}
