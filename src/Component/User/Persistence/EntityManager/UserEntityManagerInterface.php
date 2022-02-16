<?php

declare(strict_types=1);

namespace App\Component\User\Persistence\EntityManager;

use App\DataProvider\UserDataProvider;

interface UserEntityManagerInterface
{
    public function create(UserDataProvider $userDataProvider): void;

    public function update(UserDataProvider $userDataProvider): void;

    public function extendLoggedInTime(string $token): void;

    public function setToken(string $email, string $token): void;

    public function removeToken(string $token): void;

    public function delete(int $id): void;
}
