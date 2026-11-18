<?php

namespace App\Command;

use App\Service\AlertSender;
use App\Service\BorderReadingRecorder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:record-borders', description: 'Enregistre le retard de chaque douane et envoie les alertes')]
class RecordBordersCommand extends Command
{
    public function __construct(
        private BorderReadingRecorder $recorder,
        private AlertSender $alertSender,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $readings = $this->recorder->recordAll();
        $alerts = $this->alertSender->sendDueAlerts();

        $output->writeln($readings . ' relevé(s) enregistré(s), ' . $alerts . ' alerte(s) envoyée(s).');

        return Command::SUCCESS;
    }
}
