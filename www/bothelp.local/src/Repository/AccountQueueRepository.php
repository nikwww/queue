<?php

namespace App\Repository;

use App\Entity\AccountQueue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AccountQueue|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccountQueue|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccountQueue[]    findAll()
 * @method AccountQueue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountQueueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccountQueue::class);
    }

    // /**
    //  * @return AccountQueue[] Returns an array of AccountQueue objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AccountQueue
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    
    public function findOneByThreadId($value): ?AccountQueue
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.thread_id = :val')
            ->setParameter('val', $value)
            ->setMaxResults(1)
            ->orderBy('a.id')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
    public function findOneByAccountId($value): ?AccountQueue
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.account_id = :val')
            ->setParameter('val', $value)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
    public function getAvailableThread($threads_num, $max_thread_queue)
    {
        $raw_threads = $this->createQueryBuilder('a')
            ->select('a.thread_id', 'COUNT(a.id) as cnt')
            ->groupBy('a.thread_id')
            ->getQuery()
            ->getResult();

        $threads = [];
        foreach ($raw_threads as $raw_thread) {
            $threads[$raw_thread['thread_id']] = $raw_thread['cnt'];
        }

        for ($i = 1; $i <= $threads_num; $i++) {
            if (!isset($threads[$i])) {
                return $i;
            }
        }

        if (min($threads) >= $max_thread_queue) {
            return false;
        }

        return array_search(min($threads), $threads);
    }
}
