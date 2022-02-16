<?php

declare(strict_types=1);

namespace App\Component\User\Business\Model;

use App\Component\User\Persistence\EntityManager\UserEntityManagerInterface;
use App\DataProvider\ErrorDataProvider;
use App\DataProvider\UserDataProvider;

class CreateUser
{
    public function __construct(
        private UserEntityManagerInterface $userEntityManager,
        private ValidateCreate $validateCreate,
    ) {
    }
    /**
     * @return array<array-key, ErrorDataProvider>
     */
    public function create(UserDataProvider $userDataProvider): array
    {
        /** @var array<array-key, ErrorDataProvider> $errors */
        $errors = $this->validateCreate->getErrors($userDataProvider)->getErrors();

        if (empty($errors)) {
            $this->userEntityManager->create($userDataProvider);
        }

        return $errors;
    }
}
