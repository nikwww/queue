<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ManagerCommand extends Command
{
    protected static $defaultName = 'app:manager';

    private $container;
    private $threads_num;
    private $max_thread_queue;


    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->threads_num = $this->container->getParameter('threads_num');
        
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Run thread')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $processes = [];
        for ($i = 1; $i <= $this->threads_num; $i++) {
            $processes[$i] = new Process(['php', '/var/www/bothelp.local/bin/console', 'app:thread', $i]);
            $processes[$i]->setTimeout(null);
            $processes[$i]->setIdleTimeout(null);
            $processes[$i]->start();
            $io->success('thread ' . $i);
        }
        
        // можно переподнимать потоки, если упали
        
        for ($i = 1; $i <= $this->threads_num; $i++) {
            $processes[$i]->wait(function ($type, $buffer) use ($io) {
                if (Process::ERR === $type) {
                    $io->error($buffer);
                } else {
                    $io->success($buffer);
                }
            });
        }

        return Command::SUCCESS;
    }
}
