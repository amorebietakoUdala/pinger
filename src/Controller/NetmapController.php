<?php

namespace App\Controller;

use App\Entity\Ocs\Netmap;
use App\Repository\Ocs\NetmapRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/{_locale}')]
final class NetmapController extends BaseController
{
    private NetmapRepository $repo;

    public function __construct(private ManagerRegistry $doctrine)
    {
        $this->repo = $doctrine->getRepository(Netmap::class);
    }

    #[Route('/netmap', name: 'netmap_index')]
    public function index(Request $request): Response
    {
        $this->loadQueryParameters($request);
        $this->queryParams['pageSize'] = $request->get('pageSize') ?? 200;
        $netmaps = $this->repo->findAll();
        //dd($netmaps);
        return $this->render('netmap/index.html.twig', [
            'netmaps' => $netmaps,
        ]);
    }
}
