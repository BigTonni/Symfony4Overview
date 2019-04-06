<?php

namespace App\Repository;

use App\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Subscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscription[]    findAll()
 * @method Subscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    public function getTodaySubscriptionsByUserQuery($currDate, $currUser)
    {
        $from = new \DateTime($currDate->format('Y-m-d') . ' 00:00:00');
        $to = new \DateTime($currDate->format('Y-m-d') . ' 23:59:59');

        return $this->createQueryBuilder('s')
            ->where('s.user = :user_id')
            ->setParameter('user_id', $currUser)
            ->andWhere('s.createdAt BETWEEN :from AND :to')
            ->setParameter(':from', $from)
            ->setParameter(':to', $to)
            ->getQuery();
    }

    public function getTodaySubscriptionsQuery($currDate)
    {
        $from = new \DateTime($currDate->format('Y-m-d') . ' 00:00:00');
        $to = new \DateTime($currDate->format('Y-m-d') . ' 23:59:59');

        return $this->createQueryBuilder('s')
            ->andWhere('s.createdAt BETWEEN :from AND :to')
            ->setParameter(':from', $from)
            ->setParameter(':to', $to)
            ->getQuery();
    }
}
