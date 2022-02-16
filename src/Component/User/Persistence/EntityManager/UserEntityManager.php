<?php

declare(strict_types=1);

namespace App\Component\User\Persistence\EntityManager;

use App\Entity\User;
use App\DataProvider\UserDataProvider;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserEntityManager implements UserEntityManagerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    public function create(UserDataProvider $userDataProvider): void
    {
        if (empty($userDataProvider->getEmail()) || empty($userDataProvider->getPassword())) {
            throw new \RuntimeException("No data Provided");
        }

        $user = new User();

        $user
            ->setEmail($userDataProvider->getEmail())
            ->setToken($userDataProvider->getEmail())
            ->setRoles(['ROLE_USER'])
            ->setPassword($this->userPasswordHasher->hashPassword($user, $userDataProvider->getPassword()));

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function update(UserDataProvider $userDataProvider): void
    {
        if ($userDataProvider->getId() === null
            || $userDataProvider->getEmail() === null
            || $userDataProvider->getPassword() === null
        ) {
            throw new \RuntimeException("No data Provided");
        }

        $user = $this->userRepository->find($userDataProvider->getId());

        if (!$user instanceof User) {
            throw new \RuntimeException("User not found");
        }

        if ($user->getEmail() === $user->getToken()) {
            $user->setToken($userDataProvider->getEmail());
        }

        $user
            ->setEmail($userDataProvider->getEmail())
            ->setPassword($this->userPasswordHasher->hashPassword($user, $userDataProvider->getPassword()));

        $this->entityManager->flush();
    }

    public function extendLoggedInTime(string $token): void
    {
        $user = $this->userRepository->findOneBy(['token' => $token]);

        if (!$user instanceof User) {
            throw new \RuntimeException("User not found");
        }

        $user
            ->setTokenTime(new \DateTime("+ 60 Minutes"));

        $this->entityManager->flush();
    }

    public function setToken(string $email, string $token): void
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user instanceof User) {
            throw new \RuntimeException("User not found");
        }

        $user
            ->setToken($token)
            ->setTokenTime(new \DateTime("+ 60 Minutes"));

        $this->entityManager->flush();
    }

    public function removeToken(string $token): void
    {
        $user = $this->userRepository->findOneBy(['token' => $token]);

        if (!$user instanceof User) {
            throw new \RuntimeException("User not found");
        }

        $email = $user->getEmail() ?? '';

        $user
            ->setToken($email)
            ->setTokenTime(null);

        $this->entityManager->flush();
    }

    public function delete(int $id): void
    {
        $user = $this->userRepository->find($id);

        if (!$user instanceof User) {
            throw new \RuntimeException("User not found");
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}
