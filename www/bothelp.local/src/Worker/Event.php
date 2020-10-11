<?php

namespace App\Worker;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\ArrayInput;

class Event extends \Dtc\QueueBundle\Model\Worker
{
    private $app;
    private $errorlog = 'eventerror.log';

    public function __construct(KernelInterface $kernel)
    {
        try {
            $this->app = new Application($kernel);
            $this->app->setAutoExit(false);
        } catch (\Exception $e) {
            file_put_contents($this->errorlog, $e->getMessage() . "\n", FILE_APPEND);
        }
    }

    public function event($accounts)
    {
        try {
            $input = new ArrayInput([
                'command' => 'app:add_to_queue',
                'account_ids' => $accounts,
            ]);

            $output = new NullOutput();
            $this->app->run($input, $output);
        } catch (\Exception $e) {
            file_put_contents($this->errorlog, $e->getMessage() . "\n", FILE_APPEND);
        }
    }

    public function getName()
    {
        return 'event';
    }

}
