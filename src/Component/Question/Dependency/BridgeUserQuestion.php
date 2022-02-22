<?php

declare(strict_types=1);

namespace App\Component\Question\Dependency;

use App\Component\UserQuestion\Business\FacadeUserQuestionInterface;
use App\DataProvider\QuestionDataProvider;
use App\DataProvider\UserQuestionDataProvider;

class BridgeUserQuestion implements BridgeUserQuestionInterface
{
    public function __construct(
        private FacadeUserQuestionInterface $facadeUserQuestion,
    ) {
    }

    public function create(string $questionSlug, string $examSlug, int $userId): void
    {
        $this->facadeUserQuestion->create($questionSlug, $examSlug, $userId);
    }

    public function getByUserAndQuestion(int $userId, string $questionSlug): ?UserQuestionDataProvider
    {
        return $this->facadeUserQuestion->findByQuestionAndUser($userId, $questionSlug);
    }
}
