<?php

namespace App\Command;

use App\Entity\MonitorizableEvent;
use App\Repository\Default\ComputerRepository;
use App\Repository\MonitorizableEventRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Environment;

#[AsCommand('app:pinger-nohost-report', 'Send an email with the switched computers (No host) list')]
class DailyPingerNoHostReportCommand extends Command
{
    public function __construct(
        private readonly ComputerRepository $compRepo, 
        private readonly MailerInterface $mailer, 
        private Environment $twig, 
        private readonly string $mailerFrom, 
        private readonly array $mailerTo)
    {
        parent::__construct();   
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->sendMessage('[Pinger] Eguneko txostena Host ez dutenentzako', $this->mailerTo);
        return Command::SUCCESS;
    }

    /**
     * @return array $counters
     */

    private function sendMessage($subject, $emails)
    {
        $computers = $this->compRepo->getComputerByDayWithoutHost();
        $email = (new TemplatedEmail())
            ->from($this->mailerFrom)
            ->to(...$emails)
            ->subject($subject)
            ->htmlTemplate('/computer/mailnohost.html.twig')
            ->context([
                'computers' => $computers,
            ]);
        $this->mailer->send($email);
        return;
    }


}
