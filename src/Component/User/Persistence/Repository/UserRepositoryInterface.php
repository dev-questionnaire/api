<?php

declare(strict_types=1);

namespace App\Component\User\Persistence\Repository;

use App\DataProvider\UserDataProvider;

interface UserRepositoryInterface
{
    public function findById(int $id): ?UserDataProvider;

    public function checkEmailTaken(string $email, int $id): bool;

    public function findByToken(string $token): ?UserDataProvider;

    /**
     * @return array<array-key, UserDataProvider|null>
     */
    public function findAll(): array;
}
