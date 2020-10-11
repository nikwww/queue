<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Entity\AccountQueue;

class AddToQueueCommand extends Command
{
    protected static $defaultName = 'app:add_to_queue';
    private $filename = 'add_to_queue.log';

    private $em;
    private $container;
    private $threads_num;
    private $max_thread_queue;


    public function __construct(EntityManagerInterface $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
        $this->threads_num = $this->container->getParameter('threads_num');
        $this->max_thread_queue = $this->container->getParameter('max_thread_queue');
        
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Add to queue')
            ->addArgument('account_ids', InputArgument::REQUIRED, 'Account ids separator ,')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $accounts = $input->getArgument('account_ids');
        $account_ids = explode(",", $accounts);

        file_put_contents($this->filename, "{$accounts}\n", FILE_APPEND);

        foreach ($account_ids as $account_id) {
            $accountQueue = new AccountQueue();
            $accountQueue->setAccountId($account_id);
            $thread_id = $this->getThreadForAccount($account_id) ?: $this->getThread();
            $accountQueue->setThreadId($thread_id);
            $this->em->persist($accountQueue);
            $this->em->flush();
//            $io->success(print_r($thread_id, 1));
        }

        return Command::SUCCESS;
    }

    protected function getThreadForAccount($account_id)
    {
        $aQueue = $this->em->getRepository(AccountQueue::class)->findOneByAccountId($account_id);
        if ($aQueue) {
            return $aQueue->getThreadId();
        }
        
        return false;
    }

    protected function getThread()
    {
        // медод можно оптимизировать, кешируя в php свободные потоки и периодически обновляя этот кеш в команде
        $thread = $this->em->getRepository(AccountQueue::class)->getAvailableThread($this->threads_num, $this->max_thread_queue);
        
        if (!$thread) {
            // можно рвать коннект к базе
            usleep(500000);
            return $this->getThread();
        }
        
        return $thread;
    }
}
