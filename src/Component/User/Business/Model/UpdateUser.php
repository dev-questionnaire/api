<?php

declare(strict_types=1);

namespace App\Component\User\Business\Model;

use App\Component\User\Persistence\EntityManager\UserEntityManagerInterface;
use App\DataProvider\ErrorDataProvider;
use App\DataProvider\UserDataProvider;

class UpdateUser
{
    public function __construct(
        private UserEntityManagerInterface $userEntityManager,
        private ValidateUpdate $validateUpdate,
    ) {
    }
    /**
     * @return array<array-key, ErrorDataProvider>
     */
    public function update(UserDataProvider $userDataProvider): array
    {
        /** @var array<array-key, ErrorDataProvider> $errors */
        $errors = $this->validateUpdate->getErrors($userDataProvider)->getErrors();

        if (empty($errors)) {
            $this->userEntityManager->update($userDataProvider);
        }

        return $errors;
    }
}
