<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArrayInput;

class RabbitmqCommand extends Command
{
    protected static $defaultName = 'app:rabbitmq';

    protected function configure()
    {
        $this
            ->setDescription('Add to rabbitmq')
            ->addArgument('num', InputArgument::OPTIONAL, 'Num')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $num = $input->getArgument('num');

        $command = $this->getApplication()->find('dtc:queue:create_job');
        
        for ($i = 0; $i < $num; $i++) {
            $command->run(new ArrayInput(['worker_name' => 'event', 'method' => 'event', 'args' => [rand(1, 1000)]]), $output);
        }

        return Command::SUCCESS;
    }
}
