<?php

declare(strict_types=1);

namespace App\Component\UserQuestion\Persistence\Mapper;

use App\DataProvider\UserQuestionDataProvider;
use App\Entity\User;
use App\Entity\UserQuestion;

class UserQuestionMapperToArray
{
    /**
     * @return array{userId: int, answers: array<array-key, mixed>}
     */
    public function map(UserQuestionDataProvider $userQuestionDataProvider): array
    {
        return [
            'userId' => $userQuestionDataProvider->getUserId(),
            'answers' => $userQuestionDataProvider->getAnswers(),
        ];
    }
}
