<?php

declare(strict_types=1);

namespace App\Component\User\Business;

use App\Component\User\Business\Model\CreateUser;
use App\Component\User\Business\Model\FindAllMappedAsArray;
use App\Component\User\Business\Model\UpdateUser;
use App\Component\User\Persistence\EntityManager\UserEntityManagerInterface;
use App\Component\User\Persistence\Repository\UserRepositoryInterface;
use App\DataProvider\UserDataProvider;
use App\DataProvider\ErrorDataProvider;

class Facade implements FacadeInterface
{
    public function __construct(
        private CreateUser                 $createUser,
        private UpdateUser                 $updateUser,
        private FindAllMappedAsArray       $findAllMappedAsArray,
        private UserEntityManagerInterface $userEntityManager,
        private UserRepositoryInterface    $userRepository,
    ) {
    }

    public function findById(int $id): ?UserDataProvider
    {
        return $this->userRepository->findById($id);
    }

    public function checkEmailTaken(string $email, int $id): bool
    {
        return $this->userRepository->checkEmailTaken($email, $id);
    }

    public function findByToken(string $token): ?UserDataProvider
    {
        return $this->userRepository->findByToken($token);
    }

    /**
     * @return array<array-key, array<array-key, int|string|null>>
     */
    public function findAll(): array
    {
        return $this->findAllMappedAsArray->findAll();
    }

    /**
     * @return array<array-key, ErrorDataProvider>
     */
    public function create(UserDataProvider $userDataProvider): array
    {
        return $this->createUser->create($userDataProvider);
    }

    /**
     * @return array<array-key, ErrorDataProvider>
     */
    public function update(UserDataProvider $userDataProvider): array
    {
        return $this->updateUser->update($userDataProvider);
    }

    public function extendLoggedInTime(string $token): void
    {
        $this->userEntityManager->extendLoggedInTime($token);
    }

    public function setToken(string $email, string $token): void
    {
        $this->userEntityManager->setToken($email, $token);
    }

    public function removeToken(string $token): void
    {
        $this->userEntityManager->removeToken($token);
    }

    public function delete(int $userId): void
    {
        $this->userEntityManager->delete($userId);
    }
}
