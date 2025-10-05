<?php

namespace App\Command;

use App\Service\NewsletterService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:newsletter:send',
    description: 'Envoie la newsletter aux abonnés'
)]
class SendNewsletterCommand extends Command
{
    public function __construct(private NewsletterService $newsletterService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('frequency', InputArgument::OPTIONAL, 'Fréquence (weekly/monthly)', 'weekly');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $frequency = $input->getArgument('frequency');

        if (!in_array($frequency, ['weekly', 'monthly'])) {
            $io->error('La fréquence doit être "weekly" ou "monthly"');
            return Command::FAILURE;
        }

        $io->title('Envoi de la newsletter Bestio');
        $io->info("Fréquence: $frequency");

        try {
            $sentCount = $this->newsletterService->sendNewsletter($frequency);
            $io->success("Newsletter envoyée à $sentCount abonnés !");
        } catch (\Exception $e) {
            $io->error("Erreur lors de l'envoi: " . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}