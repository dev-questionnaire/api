<?php
declare(strict_types=1);

namespace App\Component\Question\Business\Model;

use App\Component\Question\Dependency\BridgeUserQuestion;
use App\Component\Question\Persistence\Mapper\QuestionMapperToArray;

class GetCurrentQuestionAndCreateUserQuestionMappedAsArray implements GetCurrentQuestionAndCreateUserQuestionMappedAsArrayInterface
{
    public function __construct(
        private BridgeUserQuestion $bridgeUserQuestion,
        private QuestionMapperToArray $questionMapperToArray,
    )
    {
    }

    public function get(array $questionDataProviderList, string $examSlug, int $userId): array
    {
        foreach ($questionDataProviderList as $questionDataProvider) {
            $userQuestionDataProvider = $this->bridgeUserQuestion->getByUserAndQuestion($userId, $questionDataProvider->getSlug());

            if ($userQuestionDataProvider === null) {
                $this->bridgeUserQuestion->create($questionDataProvider->getSlug(), $examSlug, $userId);

                return $this->questionMapperToArray->map($questionDataProvider);
            }

            if ($userQuestionDataProvider->getAnswers() === null) {
                return $this->questionMapperToArray->map($questionDataProvider);
            }
        }
        return [];
    }
}