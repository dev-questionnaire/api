<?php

declare(strict_types=1);

namespace App\Component\Exam\Dependency;

use App\Component\UserQuestion\Business\FacadeUserQuestionInterface;

class BridgeUserQuestion implements BridgeUserQuestionInterface
{
    public function __construct(
        private FacadeUserQuestionInterface $facadeUserQuestion,
    ) {
    }

    public function findByUserAndExamIndexedByQuestionSlug(int $userId, string $examSlug): array
    {
        return $this->facadeUserQuestion->findByUserAndExamIndexedByQuestionSlug($userId, $examSlug);
    }

    /**
     * @param array<array-key, \App\DataProvider\QuestionDataProvider> $questionDataProviderList
     * @param array<array-key, \App\DataProvider\UserQuestionDataProvider> $userQuestionDataProviderList
     * @param bool $isAdminPage
     * @return array{answeredCorrect?: array<array-key, bool|null>, percent?: float|int, userAnswerList?: array<array-key, array<array-key, null|string>|null>}
     */
    public function getPercentAndAnswerCorrectAndUserAnswerList(array $questionDataProviderList, array $userQuestionDataProviderList, bool $isAdminPage = false): array
    {
        return $this->facadeUserQuestion->getPercentAndAnswerCorrectAndUserAnswerList($questionDataProviderList, $userQuestionDataProviderList, $isAdminPage);
    }
}
