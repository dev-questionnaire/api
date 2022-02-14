<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/** @psalm-suppress PropertyNotSetInConstructor */
class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $user_user = new User();
        $user_user
            ->setEmail('user@cec.valantic.com')
            ->setPassword($this->userPasswordHasher->hashPassword($user_user, 'user'))
            ->setRoles(['ROLE_USER']);

        $user_admin = new User();
        $user_admin
            ->setEmail('admin@cec.valantic.com')
            ->setPassword($this->userPasswordHasher->hashPassword($user_user, 'admin'))
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($user_user);
        $manager->persist($user_admin);

        $manager->flush();
    }
}
