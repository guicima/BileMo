<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\User;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


/**
 * Class AppFixtures
 * @package App\DataFixtures
 */
class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $userPasswordHasherInterface;

    public function __construct(UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        $this->userPasswordHasherInterface = $userPasswordHasherInterface;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $user = new User();
        $user->setEmail('user@gmail.com');
        $user->setPassword($this->userPasswordHasherInterface->hashPassword($user, 'password'));
        $user->setRoles(['ROLE_USER']);
        $user->setExpiryDate(new \DateTime('+1 year'));
        $manager->persist($user);

        for ($i = 0; $i < 10; $i++) {
            $product = new Product();
            $product->setName('Product ' . $i);
            $product->setDescription(md5(rand(0, 1000000)));
            $product->setImage(md5(rand(0, 1000000)));
            $manager->persist($product);

            $client = new Client();
            $client->setFullName('Client ' . $i);
            $client->setEmail('client' . $i . '@gmail.com');
            $client->setCreatedAt(new \DateTimeImmutable());
            $client->setUserId($user);
            $manager->persist($client);
        }

        $manager->flush();
    }
}
