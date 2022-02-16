<?php

declare(strict_types=1);

namespace App\Component\User\Business\Model;

use App\DataProvider\ErrorDataProvider;
use App\DataProvider\UserDataProvider;

interface ValidateInterface
{
    public function getErrors(UserDataProvider $userDTO): ErrorDataProvider;
}
