<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = ['PHP', 'Javascript', 'Java', 'Symfony', 'React', 'Next', 'Off-topic', 'Web development', 'Laravel', 'CSS', 'DevOps', 'Testing', 'Deployment'];

        foreach ($categories as $category) {
            $newCategory = new Category();
            $newCategory->setName($category);
            $newCategory->setDescription("$category related topics");
            $newCategory->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($newCategory);
        }

        $manager->flush();
    }
}
