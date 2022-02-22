<?php

declare(strict_types=1);

namespace App\Component\UserQuestion\Business;

use App\DataProvider\QuestionDataProvider;
use App\DataProvider\UserQuestionDataProvider;

interface FacadeUserQuestionInterface
{
    public function create(string $questionSlug, string $examSlug, int $userId): void;

    public function deleteByUser(int $userId): void;

    public function findByQuestionAndUser(int $userId, string $questionSlug): ?UserQuestionDataProvider;

    /**
     * @return UserQuestionDataProvider[]
     */
    public function findByUserAndExamIndexedByQuestionSlug(int $userId, string $examSlug): array;

    /**
     * @param array<array-key, \App\DataProvider\QuestionDataProvider> $questionDataProviderList
     * @param array<array-key, \App\DataProvider\UserQuestionDataProvider> $userQuestionDataProviderList
     * @param bool $isAdminPage
     * @return array{answeredCorrect?: array<array-key, bool|null>, percent?: float|int, userAnswerList?: array<array-key, array<array-key, null|string>|null>}
     */
    public function getPercentAndAnswerCorrectAndUserAnswerList(array $questionDataProviderList, array $userQuestionDataProviderList, bool $isAdminPage): array;

    /**
     * @return list<array{userId: int, answers: array<array-key, mixed>}>
     */
    public function findByExamAndQuestionSlugMappedAsArray(string $exam, string $question): array;
}
