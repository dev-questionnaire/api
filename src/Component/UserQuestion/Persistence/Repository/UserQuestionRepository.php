<?php

declare(strict_types=1);

namespace App\Component\UserQuestion\Persistence\Repository;

use App\Component\UserQuestion\Persistence\Mapper\UserQuestionMapperToDataProvider;
use App\DataProvider\UserQuestionDataProvider;
use App\Entity\UserQuestion;
use App\Repository\UserQuestionRepository as UserQuestionEntityRepository;

class UserQuestionRepository implements UserQuestionRepositoryInterface
{
    public function __construct(
        private UserQuestionEntityRepository     $userQuestionRepository,
        private UserQuestionMapperToDataProvider $mapper,
    ) {
    }

    public function findOneByQuestionAndUser(string $questionSlug, int $userId): ?UserQuestionDataProvider
    {
        $userQuestion = $this->userQuestionRepository->findOneByQuestionAndUser($questionSlug, $userId);

        if (!$userQuestion instanceof UserQuestion) {
            return null;
        }

        return $this->mapper->map($userQuestion);
    }

    public function findByExamAndUserIndexedByQuestionSlug(string $examSlug, int $userId): array
    {
        $userQuestionDataProviderList = [];

        $userQuestionList = $this->userQuestionRepository->findByExamAndUser($examSlug, $userId);

        foreach ($userQuestionList as $userQuestion) {
            /** @var string $slug */
            $slug = $userQuestion->getQuestionSlug();

            $userQuestionDataProviderList[$slug] = $this->mapper->map($userQuestion);
        }

        return $userQuestionDataProviderList;
    }

    public function findByExamAndQuestionSlug(string $examSlug, string $questionSlug): array
    {
        $userQuestionDataProviderList = [];

        $userQuestionList = $this->userQuestionRepository->findBy(['examSlug' => $examSlug, 'questionSlug' => $questionSlug]);

        foreach ($userQuestionList as $userQuestion) {
            $userQuestionDataProviderList[] = $this->mapper->map($userQuestion);
        }

        return $userQuestionDataProviderList;
    }
}
