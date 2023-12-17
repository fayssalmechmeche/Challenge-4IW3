<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\User;
use App\Entity\Society;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUsersFixtures extends Fixture
{
    const PASSWORD = 'test';
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }
    public function load(ObjectManager $manager): void
    {
        $society = new Society();
        $society->setName('Nom de société');
        $society->setAddress('Adresse');
        $society->setPhone('0123456789');
        $society->setEmail('email@example.com');
        $society->setSiret('siret');

        $manager->persist($society);

        $admin = new User();
        $admin->setName('John');
        $admin->setLastName('Admin');
        $admin->setEmail('admin@gmail.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, SELF::PASSWORD));
        $admin->setCreatedAt(new DateTime());
        $admin->setSociety($society);
        $admin->setIsVerified(true);
        $admin->setRoles(["ROLE_ADMIN"]);

        $accountant = new User();
        $accountant->setName('John');
        $accountant->setLastName('Comptable');
        $accountant->setEmail('comptable@gmail.com');
        $accountant->setPassword($this->passwordHasher->hashPassword($accountant, SELF::PASSWORD));
        $accountant->setCreatedAt(new DateTime());
        $accountant->setSociety($society);
        $accountant->setIsVerified(true);
        $accountant->setRoles(["ROLE_ACCOUNTANT"]);

        $societyUser = new User();
        $societyUser->setName('John');
        $societyUser->setLastName('Entreprise');
        $societyUser->setEmail('entreprise@gmail.com');
        $societyUser->setPassword($this->passwordHasher->hashPassword($societyUser, SELF::PASSWORD));
        $societyUser->setCreatedAt(new DateTime());
        $societyUser->setSociety($society);
        $societyUser->setIsVerified(true);
        $societyUser->setRoles(["ROLE_SOCIETY"]);


        $user = new User();
        $user->setName('John');
        $user->setLastName('Utilisateur');
        $user->setEmail('user@gmail.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, SELF::PASSWORD));
        $user->setCreatedAt(new DateTime());
        $user->setSociety($society);
        $user->setIsVerified(true);



        $manager->persist($user);
        $manager->persist($admin);
        $manager->persist($accountant);
        $manager->persist($societyUser);

        $manager->flush();
    }
}
