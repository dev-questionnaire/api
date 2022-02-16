<?php

declare(strict_types=1);

namespace App\Component\User\Business\Model\Verification;

use App\Component\User\Persistence\Repository\UserRepository;
use App\DataProvider\ErrorDataProvider;
use App\DataProvider\UserDataProvider;

class ValidateUpdateEmail implements ValidateCollectionInterface
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function getErrors(UserDataProvider $userDTO, ErrorDataProvider $errorDataProvider): ErrorDataProvider
    {
        $email = $userDTO->getEmail();
        $id = $userDTO->getId();

        if (empty($email) || empty($id)) {
            $errorDataProvider->addError("No data provided");
            return $errorDataProvider;
        }

        if ($this->userRepository->checkEmailTaken($email, $id)) {
            $errorDataProvider->addError("Email is already taken");
        }

        if (!str_contains($email, 'nexus-united.com') && !str_contains($email, 'valantic.com')) {
            $errorDataProvider->addError("Email is not valid");
        }

        return $errorDataProvider;
    }
}
