<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TimeCommand extends Command
{
    protected static $defaultName = 'app:time';

    protected function configure()
    {
        $this->setName('time')
        ->setDescription('Shows current date and time')
        ->setHelp('This command prints the current date and time');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $now = date('c');
        $message = sprintf("Current date and time: %s", $now);

        $output->writeln($message);

        return Command::SUCCESS;
    }
}
