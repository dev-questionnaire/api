<?php

declare(strict_types=1);

namespace App\Component\User\Business;

use App\DataProvider\UserDataProvider;
use App\DataProvider\ErrorDataProvider;

interface FacadeUserInterface
{
    public function findById(int $id): ?UserDataProvider;

    public function checkEmailTaken(string $email, int $id): bool;

    public function findByToken(string $token): ?UserDataProvider;

    /**
     * @return array<array-key, array<array-key, int|string|null>>
     */
    public function getAllFormattedAsArray(): array;

    /**
     * @return array<array-key, UserDataProvider|null>
     */
    public function getAll(): array;

    /**
     * @return array<array-key, ErrorDataProvider>
     */
    public function create(UserDataProvider $userDataProvider): array;

    /**
     * @return array<array-key, ErrorDataProvider>
     */
    public function update(UserDataProvider $userDataProvider): array;

    public function extendLoggedInTime(string $token): void;

    public function setToken(string $email, string $token): void;

    public function removeToken(string $token): void;

    public function delete(int $userId): void;

    public function doesUserExist(int $id): bool;
}
