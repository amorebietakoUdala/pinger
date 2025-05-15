<?php

namespace App\Controller;

use App\Repository\Default\VendorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class ApiController extends AbstractController
{

    public function __construct(private VendorRepository $vendorRepo) {
    }

    #[Route('/vendor/{mac}', name: 'api_get_vendor')]
    public function getVendor(string $mac): Response
    {
        $vendor = $this->vendorRepo->findOneByMac($mac);
        return $this->json($vendor);
    }
}
