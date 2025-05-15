<?php

namespace App\Controller;

use App\Entity\Default\Computer;
use App\Repository\Default\ComputerRepository;
use App\Service\ArpService;
use App\Service\PingerService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/{_locale}')]
final class PingerController extends BaseController
{

    public function __construct(
        private PingerService $pinger,
        private ArpService $arp,
    ) {}

    #[Route(path: '/new_number', name: 'computer_new_number', methods: ['GET'])]
    public function newNumbers(ComputerRepository $computerRepository): Response
    {
        $nextNumber = $computerRepository->getNextNumberFromDatabase();

        return $this->render('computer/_newnumber.html.twig' , [
            'nextNumber' => $nextNumber,
        ]);
    }

    #[Route(path: '/computer/{id}/ping', name: 'computer_ping', methods: ['GET', 'POST'])]
    public function showping(Computer $computer): Response
    {
        if (null === $computer->getIp()) {
            return new JsonResponse(['status' => 'NOTOK', 'message' => 'No IP']);
        } else {
            $pingResult = $this->pinger->ping($computer);
            $parseping = $this->pinger->parsePingResult($pingResult);
            return new JsonResponse($parseping);
        }
    }

    #[Route(path: '/computer/{id}/arp', name: 'computer_arp', methods: ['GET', 'POST'])]
    public function showarp(Computer $computer): Response
    {
        if (null === $computer->getIp()) {
            return new JsonResponse(['status' => 'NOTOK', 'message' => 'No IP']);
        } else {
            $arpResult = $this->arp->arp($computer->getIp());
            $parsearp = $this->arp->parseArpResult($arpResult, $computer->getIp());
            return new JsonResponse($parsearp);
        }
    }

    
}
