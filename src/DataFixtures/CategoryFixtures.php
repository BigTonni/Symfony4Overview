<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public const CATEGORY_REFERENCE = 'category';

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $category = new Category();
        $category->setTitle('Category_1');
        $manager->persist($category);
        $manager->flush();

        $this->addReference(self::CATEGORY_REFERENCE, $category);
    }
}
