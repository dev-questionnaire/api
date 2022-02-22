<?php

declare(strict_types=1);

namespace App\Component\User\Business\Model;

use App\Component\User\Persistence\Mapper\UserMapperToArray;
use App\Component\User\Persistence\Repository\UserRepositoryInterface;
use App\DataProvider\UserDataProvider;

class FindAllFormattedAsArray
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        protected UserMapperToArray $userMapperToArray,
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
         * @var UserDataProvider $userDataProvider
         */
        foreach ($userDataProviderList as $key => $userDataProvider) {
            $id = $userDataProvider->getId() ?? $key;

            $userListJson[$id] = $this->userMapperToArray->map($userDataProvider);
        }

        return $userListJson;
    }
}
