<?php

declare(strict_types=1);

namespace App\Component\User\Dependency;

use App\Component\UserQuestion\Business\FacadeUserQuestion;

class BridgeUserQuestion implements BridgeUserQuestionInterface
{
    public function __construct(
        private FacadeUserQuestion $facadeUserQuestion,
    ) {
    }

    public function deleteByUser(int $userId): void
    {
        $this->facadeUserQuestion->deleteByUser($userId);
    }
}
