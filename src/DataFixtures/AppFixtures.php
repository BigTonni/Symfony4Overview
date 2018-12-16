<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadCategories($manager);
        $this->loadArticles($manager);
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadUsers(ObjectManager $manager): void
    {
        foreach ($this->getUserData() as [$fullName, $userName, $email, $password]) {
            $user = new User();
            $user->setFullName($fullName);
            $user->setUserName($userName);
            $user->setPassword($password);
            $user->setEmail($email);

            $manager->persist($user);
            $this->addReference($userName, $user);
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadArticles(ObjectManager $manager): void
    {
        foreach ($this->getArticleData() as [$title, $slug, $body, $publishedAt, $author]) {
            $article = new Article();
            $article->setTitle($title);
            $article->setSlug($slug);
            $article->setBody($body);
            $article->setPublishedAt($publishedAt);
            $article->setAuthor($author);

            $category = new Category();
            $category->setTitle($this->getRandomCategory());
            $manager->persist($category);

            $article->setCategory($category);

            foreach (range(1, 3) as $i) {
                $comment = new Comment();
                $comment->setContent($this->getRandomText());
                $comment->setPublishedAt(new \DateTime('now + '.$i.'seconds'));
                $comment->setAuthor($this->getReference('Author2'));

                $article->addComment($comment);
            }
            $manager->persist($article);
        }
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadCategories(ObjectManager $manager): void
    {
        foreach ($this->getCategoryData() as $index => $title) {
            $category = new Category();
            $category->setTitle($title);
            $manager->persist($category);
        }
        $manager->flush();
    }

    private function getUserData(): array
    {
        return [
            // $userData = [$fullName, $userName, $email, $password];
            ['Test Author1', 'Author1', 'test@author1.com', 'test1'],
            ['Test Author2', 'Author2', 'test@author2.com', 'test2'],
            ['Test Author3', 'Author3', 'test@author3.com', 'test3'],
        ];
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function getArticleData(): array
    {
        $articles = [];

        foreach (range(1, 4) as $i) {
            $articles[] = [
                'title-'.$i,
                'slug-'.$i,
                $this->getRandomText(),
                new \DateTime('now - '.$i.'days'),
                $this->getReference(['Author1', 'Author3'][0 === $i ? 0 : random_int(0, 1)]),
            ];
        }

        return $articles;
    }

    /**
     * @return string
     */
    private function getRandomText(): string
    {
        $arrQuote = [];
        $arrQuote[0] = 'Business related subjects.';
        $arrQuote[1] = 'Languages & Literature.';
        $arrQuote[2] = 'Six more minutes.';
        $arrQuote[3] = 'Architecture, building & planning.';
        $arrQuote[4] = 'Sport & exercise science.';
        $arrQuote[5] = 'Random quote.';

        $rand_keys = array_rand($arrQuote, 3);
        return $arrQuote[$rand_keys[0]].' '.$arrQuote[$rand_keys[1]].' '.$arrQuote[$rand_keys[2]];
    }

    /**
     * @return array
     */
    private function getCategoryData(): array
    {
        $categories = [];
        foreach (range(1, 4) as $i) {
            $categories[] = 'Cat-'.$i;
        }

        return $categories;
    }

    /**
     * @return string
     */
    private function getRandomCategory(): string
    {
        $categories = $this->getCategoryData();

        return $categories[array_rand($categories)];
    }
}
