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

        $user2 = new User();
        $user2->setEmail('userexpired@gmail.com');
        $user2->setPassword($this->userPasswordHasherInterface->hashPassword($user2, 'password'));
        $user2->setRoles(['ROLE_USER']);
        $user2->setExpiryDate(new \DateTime('-1 week'));
        $manager->persist($user2);

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
            $client->setUpdatedAt(new \DateTimeImmutable());
            $client->setUserId($user);
            $manager->persist($client);
        }

        $manager->flush();
    }
}
