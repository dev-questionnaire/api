<?php

declare(strict_types=1);

namespace App\Component\User\Business\Model;

use App\Component\User\Persistence\Repository\UserRepositoryInterface;
use App\DataProvider\UserDataProvider;
use App\Entity\User;

class FindAllMappedAsArray
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @return array<array-key, array<array-key, int|string|null>>
     */
    public function findAll(): array
    {
        $userDataProviderList = $this->userRepository->findAll();

        $userListJson = [];

        /**
         * @var int $key
         * @var User $user
         */
        foreach ($userDataProviderList as $key => $user) {
            $id = $user->getId() ?? $key;

            $userListJson[$id]['id'] = $id;
            $userListJson[$id]['email'] = $user->getEmail();
        }

        return $userListJson;
    }
}
