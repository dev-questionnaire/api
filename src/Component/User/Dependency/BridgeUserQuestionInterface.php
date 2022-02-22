<?php

declare(strict_types=1);

namespace App\Component\User\Dependency;

use App\Entity\User;

interface BridgeUserQuestionInterface
{
    public function deleteByUser(int $userId): void;
}
