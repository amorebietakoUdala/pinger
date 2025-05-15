<?php

namespace App\Controller;

use App\Entity\Default\Computer;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\NslookupService;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\JsonResponse;


#[Route('/{_locale}')]
final class NslookupController extends BaseController
{
    public function __construct(
        private NslookupService $nslookup,
    ) {}

    #[Route(path: '/computer/{id}/nslookup', name: 'computer_nslookup', methods: ['GET', 'POST'])]
    public function shownslookup(Computer $computer): Response
    {

        $nslookupResult = $this->nslookup->nslookup($computer);
        $parseNslookup = $this->nslookup->parseNslookupResult($nslookupResult);
        dd($parseNslookup);
    }
}
