<?php

namespace App\Command;

use App\Entity\Feedback360\CampaignFeedback360;
use App\Repository\Feedback360\CampaignFeedback360Repository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-states',
    description: 'Add a short description for your command',
)]
class UpdateStatesCommand extends Command
{
    public function __construct(
        private CampaignFeedback360Repository $campaignFeedback360Repository,
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            // ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            // ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        // $arg1 = $input->getArgument('arg1');

        // if ($arg1) {
        //     $io->note(sprintf('You passed an argument: %s', $arg1));
        // }

        // if ($input->getOption('option1')) {
        //     // ...
        // }
        /** @var CampaignFeedback360[] $campaigns */
        $campaigns = $this->campaignFeedback360Repository->findBy([
            'currentState' => [
                CampaignFeedback360::STATE_PROP_OPEN,
                CampaignFeedback360::STATE_PROP_EV_CLOSED,
                CampaignFeedback360::STATE_PROP_HI_CLOSED,
                CampaignFeedback360::STATE_READY
            ]
        ]);

        $nbCampaignsUpdated = 0;
        foreach ($campaigns as $campaign) {
            $beforeState = $campaign->getCurrentState();
            $campaign->autoUpdateState();
            if ($beforeState !== $campaign->getCurrentState()) {
                $msg = sprintf('Campaign %d state changed from %s to %s', $campaign->getId(), $beforeState, $campaign->getCurrentState());
                $this->logger->info($msg);
                $io->note($msg);
                $this->entityManager->persist($campaign);
                $nbCampaignsUpdated++;
            }
        }

        $this->entityManager->flush();

        $io->success(sprintf('%d campaign(s) updated successfully.', $nbCampaignsUpdated));

        return Command::SUCCESS;
    }
}
