<?php

declare(strict_types=1);

namespace App\Component\User\Persistence\Mapper;

use App\DataProvider\UserDataProvider;

class UserMapperToArray
{
    /**
     * @return array{id: int, email: string}
     */
    public function map(UserDataProvider $userDataProvider): array
    {
        $array = [];

        $array['id'] = $userDataProvider->getId();
        $array['email'] = $userDataProvider->getEmail();

        return $array;
    }
}
