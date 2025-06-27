<?php

namespace App\Controller;

use App\Entity\Default\Computer;
use App\Form\ComputerSearchFormType;
use App\Form\ComputerType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\Default\ComputerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/{_locale}')]
final class ComputerController extends BaseController
{
    public function __construct(
        private ComputerRepository $repo,
        private readonly MailerInterface $mailer,
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator,
    ) {}

    #[Route('/computer', name: 'computer_index')]
    public function index(Request $request): Response
    {
        $this->loadQueryParameters($request);

        $startIp = $request->query->get('start_ip');
        $endIp = $request->query->get('end_ip');

        $form = $this->createForm(ComputerSearchFormType::class, [
            'startIp' => $startIp,
            'endIp' => $endIp,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $startIp = $data['startIp'] ?? null;
            $endIp = $data['endIp'] ?? null;


            if (($startIp && !$endIp) || (!$startIp && $endIp)) {
                $this->addFlash('error', $this->translator->trans('message.computer.error.ip') );
                $computers = $this->repo->findAll();
                return $this->render('computer/index.html.twig', [
                    'computers' => $computers,
                    'form' => $form->createView(),
                ]);
            }

            $computers = $this->repo->findByFilter($data);
            $this->queryParams['page'] = 1;

            return $this->render('computer/index.html.twig', [
                'computers' => $computers,
                'form' => $form->createView(),
            ]);
        }

        if ($startIp === null || $endIp === null) {
            $computers = $this->repo->findAll();
        } else {
            $computers = $this->repo->findbetweenStartAndEndip($startIp, $endIp);
        }

        return $this->render('computer/index.html.twig', [
            'computers' => $computers,
            'form' => $form->createView(),
            'range' => [
                'start_ip' => $startIp,
                'end_ip' => $endIp,
            ]
        ]);
    }


    #[Route(path: '/computer/{id}', name: 'computer_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Computer $computer): Response
    {
        $this->loadQueryParameters($request);
        $form = $this->createForm(ComputerType::class, $computer, [
            'readonly' => true,
        ]);

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';
        return $this->render('computer/' . $template, [
            'computer' => $computer,
            'form' => $form,
            'readonly' => true
        ]);
    }

    #[Route(path: '/computer/{id}/edit', name: 'computer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Computer $computer): Response
    {
        $this->loadQueryParameters($request);
        $form = $this->createForm(ComputerType::class, $computer, [
            'readonly' => false,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Computer $data */
            $data = $form->getData();
            $this->em->persist($data);
            $this->em->flush();
            $this->addFlash('success', 'message.computer.saved');
            return $this->redirectToRoute('computer_index');
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';

        return $this->render('computer/' . $template, [
            'computer' => $computer,
            'form' => $form,
            'readonly' => false
        ]);
    }

    #[Route(path: '/computer/{id}/delete', name: 'computer_delete', methods: ['GET'])]
    public function delete(Request $request, Computer $computer)
    {
        if ($computer->isNecessary()) {
            $this->addFlash('error', 'message.computer.error.necessary');
            return $this->redirectToRoute('computer_index');
        }

        $this->loadQueryParameters($request);
        $this->em->remove($computer);
        $this->em->flush();
        $this->addFlash('success', 'message.computer.deleted');
        return $this->redirectToRoute('computer_index');
    }
}
