<?php

namespace App\Command;

use App\Entity\MonitorizableEvent;
use App\Repository\Default\ComputerRepository;
use App\Repository\Default\UnifiAccessPointRepository;
use App\Repository\MonitorizableEventRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Environment;

#[AsCommand('app:unifi-accesspoint-report', 'Send an email with not responding Unifi Access Points')]
class DailyUnifiAccessPointReportCommand extends Command
{
    public function __construct(
        private readonly UnifiAccessPointRepository $repo, 
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
        $this->sendMessage('[Pinger] Eguneko AccessPoint txostena', $this->mailerTo);
        return Command::SUCCESS;
    }

    /**
     * @return array $counters
     */

    private function sendMessage($subject, $emails)
    {
        // Fetch Unifi Access Points that are offline and unreachable. Send an email only if there are any.
        $aps = $this->repo->findByStateAndPingStatus('Offline', 'unreachable');
        if ($aps === null || count($aps) === 0) {
            return;
        }
        $email = (new TemplatedEmail())
            ->from($this->mailerFrom)
            ->to(...$emails)
            ->subject($subject)
            ->htmlTemplate('/unifi_ap/mail.html.twig')
            ->context([
                'aps' => $aps,
            ]);
        $this->mailer->send($email);
        return;
    }


}
