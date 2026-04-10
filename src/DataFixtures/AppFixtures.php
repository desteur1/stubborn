<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Sweatshirt;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        // =====================
        // USER TEST
        // =====================
        $user = new User();
        $user->setEmail('test@test.com');
        $user->setName('testuser');
        $user->setRoles(['ROLE_USER']);
        $user->setIsVerified(true);

        $user->setPassword(
            $this->hasher->hashPassword($user, 'password')
        );

        $manager->persist($user);

        // =====================
        // PRODUCT TEST
        // =====================
        $product = new Sweatshirt();
        $product->setName('Test Sweatshirt');
        $product->setPrice(50);
        $product->setFeatured(false);


        $product->setStockXs(10);
        $product->setStockS(10);
        $product->setStockM(10);
        $product->setStockL(10);
        $product->setStockXl(10);

        $manager->persist($product);


        $manager->flush();
    }
}