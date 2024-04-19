<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

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

        $admin = new User();
        $admin->setUsername('Admin');
        $admin->setEmail('admin@admin.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $hash = $this->passwordHasher->hashPassword($admin, 'admin1234');
        $admin->setPassword($hash);
        $admin->setCreatedAt(new \DateTimeImmutable());
        $admin->setUpdatedAt(new \DateTimeImmutable());
        $manager->persist($admin);


        $manager->flush();
    }
}
