<?php

namespace App\Repository;

use App\Entity\Like;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Like|null find($id, $lockMode = null, $lockVersion = null)
 * @method Like|null findOneBy(array $criteria, array $orderBy = null)
 * @method Like[]    findAll()
 * @method Like[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Like::class);
    }

    /**
     * @param  $article_id
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @return mixed
     */
    public function getCountLikesForArticle($article_id)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.article IN (:article_id)')
            ->setParameter(':article_id', $article_id)
            ->select('COUNT(l.id) as countLikes')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
