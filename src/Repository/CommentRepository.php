<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @throws \Exception
     * @return mixed
     */
    public function findLatest()
    {
        return $this->createQueryBuilder('com')
            ->where('com.publishedAt <= :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('com.publishedAt', 'DESC')
            ->setMaxResults(Comment::NUM_ITEMS)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getCountUserComments($user_id)
    {
        return $this->createQueryBuilder('com')
            ->where('com.author = :user_id')
            ->setParameter('user_id', $user_id)
            ->select('COUNT(com.id) as countComments')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
