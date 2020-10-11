<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\AccountQueue;

class ThreadCommand extends Command
{
    protected static $defaultName = 'app:thread';
    private $filename = 'processing.log';

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Thread')
            ->addArgument('thread', InputArgument::REQUIRED, 'Thread number')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $thread = $input->getArgument('thread');
        
        while (true) {
            $aQueue = $this->em->getRepository(AccountQueue::class)->findOneByThreadId($thread);
            if (!$aQueue) {
                usleep(500000);
                continue;
            }
            
            // максимально легкий тред, но распределитель очереди тяжелый
            sleep(1); // do something

            set_time_limit(60);
            
            file_put_contents($this->filename, "{$thread}: {$aQueue->getId()} done\n", FILE_APPEND);
            
            $this->em->remove($aQueue);
            $this->em->flush();
        }

        return Command::SUCCESS;
    }
}
