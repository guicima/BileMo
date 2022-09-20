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
            $product->setName(rand(0, 1) == 1 ? 'iPhone ' . rand(8, 12) : 'Samsung S' . rand(9, 53));
            $product->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec auctor, nisl eget aliquam ultricies, nunc nisl aliquam nisl, eget aliquam nunc nisl eget nunc.');
            $product->setImage('https://www.backmarket.fr/cdn-cgi/image/format=auto,quality=75,width=640/https://d1eh9yux7w8iql.cloudfront.net/product_images/36827_24756a33-907f-4a5a-ac95-73ce492104e7.jpg');
            $product->setPrice(rand(100, 1000));
            $product->setHdd('128GB');
            $product->setRam('4GB');
            $product->setCpu('A13 Bionic');
            $product->setScreenSize('6.1"');
            $product->setScreenResolution('1792 x 828');
            $product->setBattery('3110mAh');
            $product->setConnectivity('5G');

            $manager->persist($product);

            $client = new Client();
            $client->setFullName('Client ' . $i);
            $client->setEmail('client' . $i . '@gmail.com');
            $client->setCreatedAt(new \DateTimeImmutable());
            $client->setUpdatedAt(new \DateTimeImmutable());
            $client->setUserId(rand(0, 1) == 1 ? $user : $user2);
            $manager->persist($client);
        }

        $manager->flush();
    }
}
