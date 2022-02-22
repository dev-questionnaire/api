<?php

declare(strict_types=1);

namespace App\Component\UserQuestion\Persistence\EntityManager;

use App\DataProvider\UserQuestionDataProvider;

interface UserQuestionEntityManagerInterface
{
    public function create(UserQuestionDataProvider $userQuestionDataProvider): void;

    public function updateAnswer(UserQuestionDataProvider $userQuestionDataProvider): void;

    public function deleteByUser(int $userId): void;
}
