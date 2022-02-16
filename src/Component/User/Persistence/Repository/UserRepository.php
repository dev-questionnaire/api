<?php

declare(strict_types=1);

namespace App\Component\User\Persistence\Repository;

use App\Component\User\Persistence\Mapper\UserMapper;
use App\DataProvider\UserDataProvider;
use App\Entity\User;
use App\Repository\UserRepository as UserEntityRepository;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private UserMapper $userMapper,
        private UserEntityRepository $userEntityRepository,
    ) {
    }

    public function findById(int $id): ?UserDataProvider
    {
        $user = $this->userEntityRepository->find($id);

        if (!$user instanceof User) {
            return null;
        }

        return $this->userMapper->map($user);
    }

    public function findByToken(string $token): ?UserDataProvider
    {
        $user = $this->userEntityRepository->findOneBy(['token' => $token]);

        if (!$user instanceof User) {
            return null;
        }

        return $this->userMapper->map($user);
    }

    public function checkEmailTaken(string $email, int $id): bool
    {
        $user = $this->userEntityRepository->findByEmailExcludeId($email, $id);

        if (empty($user)) {
            return false;
        }

        return true;
    }


    public function findAll(): array
    {
        $userDataProviderList = [];

        $userList = $this->userEntityRepository->findAll();

        foreach ($userList as $key => $user) {
            $id = $user->getId() ?? $key;
            $userDataProviderList[$id] = $this->userMapper->map($user);
        }

        return $userDataProviderList;
    }
}
