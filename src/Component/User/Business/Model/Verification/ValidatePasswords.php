<?php

declare(strict_types=1);

namespace App\Component\User\Business\Model\Verification;

use App\DataProvider\ErrorDataProvider;
use App\DataProvider\UserDataProvider;

class ValidatePasswords implements ValidateCollectionInterface
{
    public function getErrors(UserDataProvider $userDTO, ErrorDataProvider $errorDataProvider): ErrorDataProvider
    {
        $password = $userDTO->getPassword();
        $verPassword = $userDTO->getVerificationPassword();

        if (empty($password)) {
            $errorDataProvider->addError("No Password provided!");
            return $errorDataProvider;
        }

        if (empty($verPassword)) {
            $errorDataProvider->addError("No Verification Password provided!");
            return $errorDataProvider;
        }

        if (strlen($password) < 8) {
            $errorDataProvider->addError("Password too short!");
        }

        if (!preg_match("#[\d]+#", $password)) {
            $errorDataProvider->addError("Password must include at least one number!");
        }

        if (!preg_match("#[a-z]+#", $password)) {
            $errorDataProvider->addError("Password must include at least one lowercase letter!");
        }

        if (!preg_match("#[A-Z]+#", $password)) {
            $errorDataProvider->addError("Password must include at least one uppercase letter!");
        }

        if (!preg_match("/[!@#$%^&*-]+/", $password)) {
            $errorDataProvider->addError("Password must include at one special character!");
        }

        if ($password !== $verPassword) {
            $errorDataProvider->addError("Password musst match Verification Password!");
        }

        return $errorDataProvider;
    }
}
