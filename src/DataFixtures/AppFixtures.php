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
    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadComments($manager);
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
        }
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadComments(ObjectManager $manager): void
    {
        $comment = new Comment();
        //code...
        $manager->persist($comment);
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadArticles(ObjectManager $manager): void
    {
        $article = new Article();
        //code...
        $manager->persist($article);
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadCategories(ObjectManager $manager): void
    {
        $category = new Category();
        //code...
        $manager->persist($category);
        $manager->flush();
    }

    private function getUserData(): array
    {
        return [
            // $userData = [$fullName, $userName, $email, $password];
            ['fullName' => 'Test Author1', 'username' => 'Author1', 'email' => 'test@author1.com', 'password' => 'test1'],
            ['fullName' => 'Test Author2', 'username' => 'Author2', 'email' => 'test@author2.com', 'password' => 'test2'],
            ['fullName' => 'Test Author3', 'username' => 'Author3', 'email' => 'test@author3.com', 'password' => 'test3'],
        ];
    }
}
