<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Subscription;
use App\Entity\User;
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

    /**
     * @param Category $category
     * @param User     $user
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @return mixed
     */
    public function deleteByCatagoryAndUser(Category $category, User $user)
    {
        return $this->createQueryBuilder('s')
            ->delete()
            ->where('s.category = :category')
            ->andWhere('s.user = :user')
            ->setParameter(':category', $category)
            ->setParameter(':user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
