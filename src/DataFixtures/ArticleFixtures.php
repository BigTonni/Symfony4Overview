<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    public const ARTICLE_REFERENCE = 'article';

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $article = new Article();
        $article->setTitle('title_1');
        $article->setSlug('slug_1');
        $article->setBody('Random content.');
        $article->setPublishedAt(new \DateTime('now'));
        $article->setAuthor($this->getReference(UserFixtures::USER_REFERENCE));
        $article->setCategory($this->getReference(CategoryFixtures::CATEGORY_REFERENCE));
        $article->addTag($this->getReference(TagFixtures::TAG_REFERENCE));
//        $article->addComment($this->getReference(CommentFixtures::COMMENT_REFERENCE));

        $manager->persist($article);
        $manager->flush();
    }

    /**
     * @return array
     */
    public function getDependencies()
    {
        return [UserFixtures::class, CategoryFixtures::class, TagFixtures::class];
    }
}
