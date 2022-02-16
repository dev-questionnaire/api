<?php

declare(strict_types=1);

namespace App\Component\User\Business\Model\Verification;

use App\DataProvider\ErrorDataProvider;
use App\DataProvider\UserDataProvider;
use App\Entity\User;
use App\Repository\UserRepository;

class ValidateRegistrationEmail implements ValidateCollectionInterface
{
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    public function getErrors(UserDataProvider $userDTO, ErrorDataProvider $errorDataProvider): ErrorDataProvider
    {
        $email = $userDTO->getEmail();

        if (empty($email)) {
            $errorDataProvider->addError("No email provided");
            return $errorDataProvider;
        }

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if ($user instanceof User) {
            $errorDataProvider->addError("Email is already taken");
        }

        if (!str_contains($email, 'nexus-united.com') && !str_contains($email, 'valantic.com')) {
            $errorDataProvider->addError("Email is not valid");
        }

        return $errorDataProvider;
    }
}
