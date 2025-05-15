<?php

namespace App\Controller;

use App\Entity\Ocs\Hardware;
use App\Repository\Ocs\HardwareRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/{_locale}')]
final class HardwareController extends BaseController
{

    private HardwareRepository $repo;

    public function __construct(private ManagerRegistry $doctrine)
    {
        $this->repo = $doctrine->getRepository(Hardware::class);
    }

    #[Route('/hardware', name: 'hardware_index')]
    public function index(Request $request): Response
    {
        $this->loadQueryParameters($request);
        $this->queryParams['pageSize'] = $request->get('pageSize') ?? 200;

        $hardwares = $this->repo->findAll();
        
        return $this->render('hardware/index.html.twig', [
            'hardwares' => $hardwares,
        ]);
    }
}
