<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'dev:tailwind',
    description: 'Add a short description for your command',
)]
class DevTailwindCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Starting Tailwind CSS in watch mode...');
        $pipes = [];
        $process = proc_open(
            'tailwindcss -i public/css/input.css -o public/css/output.css --watch',
            [
                0 => STDIN,
                1 => STDOUT,
                2 => STDERR
            ],
            $pipes
        );
        $io->success('Tailwind CSS is now watching for changes...');
            // Keep the command running
        while (is_resource($process) && ($status = proc_get_status($process)) && $status['running']) {
            sleep(1);
        }

        if (is_resource($process)) {
            proc_close($process);
        }

        return Command::SUCCESS;
    }
}
