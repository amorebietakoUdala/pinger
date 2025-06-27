<?php

namespace App\Controller;

use App\Entity\Default\Phone;
use App\Repository\Default\PhoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class PhoneController extends BaseController
{

    public function __construct(
        private HttpClientInterface $httpClient, 
        private LoggerInterface $logger,
        private PhoneRepository $repo,
        private EntityManagerInterface $em,
        )
    {
        // You can inject services here if needed
    }

    #[Route('/phone/register', name: 'phone_register', methods: ['GET'])]
    public function register(Request $request): Response
    {
        $this->logger->info('Registering phone with data: ' . json_encode($request->query->all()));
        $phoneData = $request->query->all();
        /** @var Phone $phone */
        $phone = $this->repo->findOneBy(['mac' => $phoneData['mac']]);
        if (null === $phone) {
            $this->logger->info('New Phone: ' . json_encode($request->query->all()));
            $phone = new Phone();
        } else {
            $this->logger->info('Updating Phone: ' . json_encode($request->query->all()));
        }
        $phone->fillFromArray($phoneData);
        $this->em->persist($phone);
        $this->em->flush();
        return new Response('Phone registered successfully', Response::HTTP_OK);
    }

    #[Route('/{_locale}/phone', name: 'phone_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $this->loadQueryParameters($request);

        $phones = $this->repo->findAll();

        return $this->render('phone/index.html.twig', [
            'phones' => $phones,
        ]);
    }


}
