<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Society;
use App\Service\Stripe\StripeService;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUsersFixtures extends Fixture
{
    const PASSWORD = 'test';
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher, private StripeService $stripeService)
    {
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {
            $society = new Society();
            $society->setName($faker->company());
            $society->setAddress($faker->address());
            $society->setPhone($faker->numerify('07########'));
            $society->setEmail('email' . $i . '@example.com');
            $society->setSiret('12345678912345');
            $this->stripeService->createCustomer($society);
            $manager->persist($society);

            $admin = new User();
            $admin->setName($faker->firstName());
            $admin->setLastName($faker->lastName());
            $admin->setEmail('admin' . $i . '@gmail.com');
            $admin->setPassword($this->passwordHasher->hashPassword($admin, SELF::PASSWORD));
            $admin->setCreatedAt(new DateTime());
            $admin->setSociety($society);
            $admin->setIsVerified(true);
            $admin->setRoles(["ROLE_ADMIN"]);

            $head = new User();
            $head->setName($faker->firstName());
            $head->setLastName($faker->lastName());
            $head->setEmail('chef' . $i . '@gmail.com');
            $head->setPassword($this->passwordHasher->hashPassword($head, SELF::PASSWORD));
            $head->setCreatedAt(new DateTime());
            $head->setSociety($society);
            $head->setIsVerified(true);
            $head->setRoles(["ROLE_HEAD"]);


            $accountant = new User();
            $accountant->setName($faker->firstName());
            $accountant->setLastName($faker->lastName());
            $accountant->setEmail('comptable' . $i . '@gmail.com');
            $accountant->setPassword($this->passwordHasher->hashPassword($accountant, SELF::PASSWORD));
            $accountant->setCreatedAt(new DateTime());
            $accountant->setSociety($society);
            $accountant->setIsVerified(true);
            $accountant->setRoles(["ROLE_ACCOUNTANT"]);


            $societyUser = new User();
            $societyUser->setName($faker->firstName());
            $societyUser->setLastName($faker->lastName());
            $societyUser->setEmail('entreprise' . $i . '@gmail.com');
            $societyUser->setPassword($this->passwordHasher->hashPassword($societyUser, SELF::PASSWORD));
            $societyUser->setCreatedAt(new DateTime());
            $societyUser->setSociety($society);
            $societyUser->setIsVerified(true);
            $societyUser->setRoles(["ROLE_SOCIETY"]);


            $user = new User();
            $user->setName($faker->firstName());
            $user->setLastName($faker->lastName());
            $user->setEmail('user' . $i . '@gmail.com');
            $user->setPassword($this->passwordHasher->hashPassword($user, SELF::PASSWORD));
            $user->setCreatedAt(new DateTime());
            $user->setSociety($society);
            $user->setIsVerified(true);
            $manager->persist($head);
            $manager->persist($user);
            $manager->persist($admin);
            $manager->persist($accountant);
            $manager->persist($societyUser);
            $manager->flush();
        }
    }
}
