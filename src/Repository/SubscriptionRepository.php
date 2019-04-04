<?php

namespace App\Repository;

//use App\Entity\Category;
use App\Entity\Subscription;
//use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Subscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscription[]    findAll()
 * @method Subscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriptionRepository extends ServiceEntityRepository
{
    private $em;

    public function __construct(RegistryInterface $registry, EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct($registry, Subscription::class);
    }

    public function getAllSubscribers() {
//        $rsm = new ResultSetMapping();
//        $query = $this->em->createNativeQuery('SELECT user_id FROM subscription GROUP BY user_id', $rsm);
//
//        $subscribers = $query->getResult();

        $subscribers = $this->createQueryBuilder('s')
            ->select('s.user_id')
            ->groupBy('user_id')
            ->getQuery()
            ->getResult();

        return $subscribers;
    }
}
