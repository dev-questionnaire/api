<?php
declare(strict_types=1);

namespace App\Component\UserQuestion\Business\Model;

interface GetPercentAndAnswerCorrectAndUserAnswerListInterface
{
    /**
     * @param array<array-key, \App\DataProvider\QuestionDataProvider> $questionDataProviderList
     * @param array<array-key, \App\DataProvider\UserQuestionDataProvider> $userQuestionDataProviderList
     * @return array{answeredCorrect?: array<array-key, bool|null>, percent?: float|int, userAnswerList?: array<array-key, array<array-key, null|string>|null>}
     */
    public function get(array $questionDataProviderList, array $userQuestionDataProviderList, bool $isAdminPage = false): array;
}

