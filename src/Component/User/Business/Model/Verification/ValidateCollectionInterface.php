<?php

declare(strict_types=1);

namespace App\Component\User\Business\Model\Verification;

use App\DataProvider\ErrorDataProvider;
use App\DataProvider\UserDataProvider;

interface ValidateCollectionInterface
{
    public function getErrors(UserDataProvider $userDTO, ErrorDataProvider $errorDataProvider): ErrorDataProvider;
}
