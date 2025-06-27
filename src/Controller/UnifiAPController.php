<?php

namespace App\Controller;

use App\Entity\Default\UnifiAccessPoint;
use App\Form\UnifiAccessPointType;
use App\Repository\Default\UnifiAccessPointRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use UniFi_API\Client;

#[Route('/{_locale}')]
final class UnifiAPController extends BaseController
{
    private Client $unifiConnection;

    public function __construct(
        private $unifiControllerDebug, 
        private $unifiControllerUser, 
        private $unifiControllerPassword, 
        private $unifiControllerUrl, 
        private $unifiControllerSiteId, 
        private $unifiControllerVersion,
        private UnifiAccessPointRepository $repo,
        private readonly EntityManagerInterface $em,
    ) {
        /**
         * initialize the UniFi API connection class and log in to the controller and pull the requested data
         */
        $this->unifiConnection = new Client(
            $this->unifiControllerUser,
            $this->unifiControllerPassword,
            $this->unifiControllerUrl,
            $this->unifiControllerSiteId,
            $this->unifiControllerVersion
        );
    }

    #[Route('/unifi/aps', name: 'unifi_aps_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $this->loadQueryParameters($request);

        $unifiAps = $this->repo->findAll();

        return $this->render('unifi_ap/index.html.twig', [
            'unifiAps' => $unifiAps,
        ]);
    }

    #[Route(path: '/unifi/aps{id}', name: 'unifi_aps_show', methods: ['GET', 'POST'])]
    public function show(Request $request, UnifiAccessPoint $ap): Response
    {
        $this->loadQueryParameters($request);
        $form = $this->createForm(UnifiAccessPointType::class, $ap, [
            'readonly' => true,
        ]);

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';
        return $this->render('unifi_ap/' . $template, [
            'ap' => $ap,
            'form' => $form,
            'readonly' => true
        ]);
    }

    #[Route(path: '/unifi/aps/{id}/edit', name: 'unifi_aps_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UnifiAccessPoint $ap): Response
    {
        $this->loadQueryParameters($request);
        $form = $this->createForm(UnifiAccessPointType::class, $ap, [
            'readonly' => false,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UnifiAccessPoint $data */
            $data = $form->getData();
            $this->em->persist($data);
            $this->em->flush();
            $this->addFlash('success', 'message.unifiAp.saved');
            return $this->redirectToRoute('unifi_aps_index');
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';

        return $this->render('unifi_ap/' . $template, [
            'ap' => $ap,
            'form' => $form,
            'readonly' => false
        ]);
    }

    #[Route(path: '/unifi/aps/{id}/delete', name: 'unifi_aps_delete', methods: ['GET'])]
    public function delete(Request $request, UnifiAccessPoint $ap)
    {
        $this->loadQueryParameters($request);
        $this->em->remove($ap);
        $this->em->flush();
        $this->addFlash('success', 'message.access.deleted');
        return $this->redirectToRoute('unifi_aps_index');
    }

}