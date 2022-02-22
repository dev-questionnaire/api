<?php

declare(strict_types=1);

namespace App\Component\Question\Dependency;

use App\DataProvider\QuestionDataProvider;
use App\DataProvider\UserQuestionDataProvider;

interface BridgeUserQuestionInterface
{
    public function create(string $questionSlug, string $examSlug, int $userId): void;

    public function getByUserAndQuestion(int $userId, string $questionSlug): ?UserQuestionDataProvider;
}
