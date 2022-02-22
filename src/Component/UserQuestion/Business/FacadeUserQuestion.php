<?php

declare(strict_types=1);

namespace App\Component\UserQuestion\Business;

use App\Component\UserQuestion\Business\Model\FindByExamAndQuestionSlugMappedAsArrayInterface;
use App\Component\UserQuestion\Business\Model\GetPercentAndAnswerCorrectAndUserAnswerListInterface;
use App\Component\UserQuestion\Persistence\EntityManager\UserQuestionEntityManagerInterface;
use App\Component\UserQuestion\Persistence\Repository\UserQuestionRepositoryInterface;
use App\DataProvider\QuestionDataProvider;
use App\DataProvider\UserQuestionDataProvider;

class FacadeUserQuestion implements FacadeUserQuestionInterface
{
    public function __construct(
        private UserQuestionRepositoryInterface                      $userQuestionRepository,
        private UserQuestionEntityManagerInterface                   $userQuestionEntityManager,
        private GetPercentAndAnswerCorrectAndUserAnswerListInterface $getPercentAndAnswerCorrectAndUserAnswerList,
        private FindByExamAndQuestionSlugMappedAsArrayInterface      $findByExamAndQuestionSlugMappedAsArray,
    )
    {
    }

    public function create(string $questionSlug, string $examSlug, int $userId): void
    {
        $userQuestionDataProvider = new UserQuestionDataProvider();

        $userQuestionDataProvider
            ->setAnswers(null)
            ->setUserId($userId)
            ->setQuestionSlug($questionSlug)
            ->setExamSlug($examSlug);

        $this->userQuestionEntityManager->create($userQuestionDataProvider);
    }

    public function deleteByUser(int $userId): void
    {
        $this->userQuestionEntityManager->deleteByUser($userId);
    }

    public function findByQuestionAndUser(int $userId, string $questionSlug): ?UserQuestionDataProvider
    {
        return $this->userQuestionRepository->findOneByQuestionAndUser($questionSlug, $userId);
    }

    public function findByUserAndExamIndexedByQuestionSlug(int $userId, string $examSlug): array
    {
        return $this->userQuestionRepository->findByExamAndUserIndexedByQuestionSlug($examSlug, $userId);
    }

    /**
     * @param array<array-key, \App\DataProvider\QuestionDataProvider> $questionDataProviderList
     * @param array<array-key, \App\DataProvider\UserQuestionDataProvider> $userQuestionDataProviderList
     * @param bool $isAdminPage
     * @return array{answeredCorrect?: array<array-key, bool|null>, percent?: float|int, userAnswerList?: array<array-key, array<array-key, null|string>|null>}
     */
    public function getPercentAndAnswerCorrectAndUserAnswerList(array $questionDataProviderList, array $userQuestionDataProviderList, bool $isAdminPage = false): array
    {
        return $this->getPercentAndAnswerCorrectAndUserAnswerList->get($questionDataProviderList, $userQuestionDataProviderList, $isAdminPage);
    }

    public function findByExamAndQuestionSlugMappedAsArray(string $exam, string $question): array
    {
        return $this->findByExamAndQuestionSlugMappedAsArray->find($exam, $question);
    }
}
