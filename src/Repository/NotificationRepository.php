<?php

namespace App\Repository;
//
//use App\Entity\Article;
//use App\Entity\Notification;
//use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
//use Symfony\Bridge\Doctrine\RegistryInterface;
//
///**
// * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
// * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
// * @method Notification[]    findAll()
// * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
// */
class NotificationRepository extends ServiceEntityRepository
{
//    /**
//     * NotificationRepository constructor.
//     *
//     * @param RegistryInterface $registry
//     */
//    public function __construct(RegistryInterface $registry)
//    {
//        parent::__construct($registry, Notification::class);
//    }

//    /**
//     * @param Article $article
//     * @param User $user
//     * @param bool $status
//     *
//     * @throws \Doctrine\ORM\NonUniqueResultException
//     * @return mixed
//     */
//    public function updateReadStatus(Article $article, User $user, bool $status)
//    {
//        return $this->createQueryBuilder('u')
//            ->update()
//            ->set('u.isRead', ':status')
//            ->where('u.article = :article')
//            ->andWhere('u.user = :user')
//            ->setParameter(':status', $status)
//            ->setParameter(':article', $article)
//            ->setParameter(':user', $user)
//            ->getQuery()
//            ->getSingleScalarResult();
//    }

//    /**
//     * @param bool $status
//     *
//     * @return mixed
//     */
//    public function selectUsersByReadStatus($status)
//    {
//        return $this->createQueryBuilder('n')
//            ->innerJoin('n.user', 'u')
//            ->where('n.isRead = :status')
//            ->setParameter('status', $status)
//            ->getQuery()
//            ->getResult();
//    }
}
