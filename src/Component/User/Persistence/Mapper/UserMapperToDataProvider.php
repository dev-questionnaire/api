<?php

declare(strict_types=1);

namespace App\Component\User\Persistence\Mapper;

use App\DataProvider\UserDataProvider;
use App\Entity\User;

class UserMapperToDataProvider
{
    public function map(User $user): ?UserDataProvider
    {
        $userDataProvider = new UserDataProvider();
        $userTokenTime = $user->getTokenTime();

        $userDataProvider
            ->setId($user->getId())
            ->setEmail($user->getEmail())
            ->setRoles($user->getRoles());

        if ($userTokenTime === null) {
            $userDataProvider->setTokenTime(null);
            return $userDataProvider;
        }

        $tokenTime = new \DateTime($userTokenTime->format('Y-m-d H:i:s'));

        $userDataProvider->setTokenTime($tokenTime);

        return $userDataProvider;
    }
}
