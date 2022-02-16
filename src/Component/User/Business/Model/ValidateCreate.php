<?php

declare(strict_types=1);

namespace App\Component\User\Business\Model;

use App\DataProvider\ErrorDataProvider;
use App\DataProvider\UserDataProvider;
use App\Component\User\Business\Model\Verification\ValidateCollectionInterface;

class ValidateCreate implements ValidateInterface
{
    /**
     * @var ValidateCollectionInterface[]
     */
    private array $validateCollection;

    public function __construct(ValidateCollectionInterface...$validateCollection)
    {
        $this->validateCollection = $validateCollection;
    }

    public function getErrors(UserDataProvider $userDTO): ErrorDataProvider
    {
        $errorDataProvider = new ErrorDataProvider();

        foreach ($this->validateCollection as $validateObject) {
            $errorDataProvider = $validateObject->getErrors($userDTO, $errorDataProvider);
        }

        return $errorDataProvider;
    }
}
