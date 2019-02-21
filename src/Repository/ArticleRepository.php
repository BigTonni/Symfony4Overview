<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * @return mixed
     */
    public function findLatest()
    {
        return $this->createQueryBuilder('a')
            ->where('a.status = :status')
            ->setParameter('status', 2)
            ->orderBy('a.createdAt', 'Desc')
            ->setMaxResults(Article::NUM_ITEMS)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findArticlesByCategoryId($id)
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.category', 'c')
            ->where('c.id = :id')
            ->setParameter(':id', $id)
            ->orderBy('c.title', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findArticlesByTagId($id)
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.tags', 't')
            ->where('t.id = :id')
            ->setParameter(':id', $id)
            ->orderBy('t.name', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findTodayArticlesByCategoryId($id)
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.category', 'c')
            ->where('c.id = :id')
            ->andWhere('a.createdAt <= :now')
            ->setParameter(':id', $id)
            ->setParameter(':now', new \DateTime())
            ->orderBy('a.createdAt', 'Desc')
            ->setMaxResults(Article::NUM_ITEMS)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $user_id
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @return mixed
     */
    public function getCountUserArticles($user_id)
    {
        return $this->createQueryBuilder('a')
            ->where('a.author = :user_id')
            ->setParameter('user_id', $user_id)
            ->select('COUNT(a.id) as countArticles')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * @param string $rawQuery
     * @param int $limit
     * @return Article[]
     */
    public function findBySearchQuery(string $rawQuery, int $limit = Article::NUM_ITEMS): array
    {
        $query = $this->sanitizeSearchQuery($rawQuery);
        $searchTerms = $this->extractSearchTerms($query);

        if (0 === \count($searchTerms)) {
            return [];
        }

        $queryBuilder = $this->createQueryBuilder('a');

        foreach ($searchTerms as $key => $term) {
            $queryBuilder
                ->orWhere('a.title LIKE :t_' . $key)
                ->setParameter('t_' . $key, '%' . $term . '%')
            ;
        }

        return $queryBuilder
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Removes all non-alphanumeric characters except whitespaces.
     * @param string $query
     * @return string
     */
    private function sanitizeSearchQuery(string $query): string
    {
        return trim(preg_replace('/[[:space:]]+/', ' ', $query));
    }

    /**
     * Splits the search query into terms and removes the ones which are irrelevant.
     * @param string $searchQuery
     * @return array
     */
    private function extractSearchTerms(string $searchQuery): array
    {
        $terms = array_unique(explode(' ', $searchQuery));

        return array_filter($terms, function ($term) {
            return 2 <= mb_strlen($term);
        });
    }
}
