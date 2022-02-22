<?php
declare(strict_types=1);

namespace App\Component\UserQuestion\Business\Model;

use App\Component\UserQuestion\Persistence\Mapper\UserQuestionMapperToArray;
use App\Component\UserQuestion\Persistence\Repository\UserQuestionRepositoryInterface;

class FindByExamAndQuestionSlugMappedAsArray implements FindByExamAndQuestionSlugMappedAsArrayInterface
{
    public function __construct(
        private UserQuestionRepositoryInterface $userQuestionRepository,
        private UserQuestionMapperToArray       $userQuestionMapperToArray,
    )
    {
    }

    public function find(string $exam, string $question): array
    {
        $userQuestionDataProviderList = $this->userQuestionRepository->findByExamAndQuestionSlug($exam, $question);

        $array = [];

        foreach ($userQuestionDataProviderList as $userQuestionDataProvider) {
            $array[] = $this->userQuestionMapperToArray->map($userQuestionDataProvider);
        }

        return $array;
    }
}