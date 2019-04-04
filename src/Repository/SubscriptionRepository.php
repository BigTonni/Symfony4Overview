<?php

namespace App\Repository;

//use App\Entity\Category;
use App\Entity\Subscription;
//use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bridge\Doctrine\RegistryInterface;
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
        $rsm = new ResultSetMapping();
        $query = $this->em->createNativeQuery('SELECT IDENTITY(user_id) AS userId FROM subscription GROUP BY userId', $rsm);

        $subscribers = $query->getResult();

//        $subscribers = $this->createQueryBuilder('s')
//            ->select('IDENTITY(s.user)')
//            ->groupBy('s.user')
//            ->getQuery()
//            ->getResult();
dd($subscribers);
        return $subscribers;
    }
//    public function getAllSubscribers() {
//        $subscribers = $this->createQueryBuilder('s')
//            ->select('IDENTITY(s.user), s.category')
//            ->groupBy('s.user')
//            ->getQuery()
//            ->getResult();
//
//        return $subscribers;
//    }
}
