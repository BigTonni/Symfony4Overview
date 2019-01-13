<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public const COMMENT_REFERENCE = 'comment';

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $comment = new Comment();
        $comment->setContent('Random quote.');
        $comment->setPublishedAt(new \DateTime('now'));
        $comment->setAuthor($this->getReference(UserFixtures::USER_REFERENCE));

        $comment->setArticle($this->getReference(ArticleFixtures::ARTICLE_REFERENCE));
        dd($comment);
        die;
        $manager->persist($comment);
        $manager->flush();

        $this->addReference(self::COMMENT_REFERENCE, $comment);
    }

    /**
     * @return array
     */
    public function getDependencies()
    {
        return [UserFixtures::class, ArticleFixtures::class];
    }
}
