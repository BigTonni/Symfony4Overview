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
     * @param int $count
     * @return mixed
     */
    public function findOldest(int $count = 10)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.publishedAt <= :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('a.publishedAt', 'ASC')
            ->setMaxResults($count)
            ->getQuery()
            ->getResult()
            ;
    }
}
