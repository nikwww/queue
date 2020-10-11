<?php

namespace App\Entity;

use App\Repository\AccountQueueRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AccountQueueRepository::class)
 */
class AccountQueue
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $account_id;
    
    private $filename = 'processing';

    /**
     * @ORM\Column(type="integer")
     */
    private $thread_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccountId(): ?int
    {
        return $this->account_id;
    }

    public function setAccountId(int $account_id): self
    {
        $this->account_id = $account_id;

        return $this;
    }

    public function getThreadId(): ?int
    {
        return $this->thread_id;
    }

    public function setThreadId(int $thread_id): self
    {
        $this->thread_id = $thread_id;

        return $this;
    }
    
    public function runTask()
    {
        sleep(1);
        file_put_contents($this->filename, "{$this->getAccountId()} done\n", FILE_APPEND);
    }
}
