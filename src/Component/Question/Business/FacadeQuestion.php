<?php

declare(strict_types=1);

namespace App\Component\Question\Business;

use App\Component\Question\Business\Model\FindByExamSlugMappedAsArray;
use App\Component\Question\Business\Model\FindByExamSlugMappedAsArrayInterface;
use App\Component\Question\Business\Model\FindOneByExamAndQuestionSlugInterface;
use App\Component\Question\Business\Model\GetCurrentQuestionAndCreateUserQuestionMappedAsArray;
use App\Component\Question\Business\Model\GetCurrentQuestionAndCreateUserQuestionMappedAsArrayInterface;
use App\Component\Question\Persistence\Repository\QuestionRepositoryInterface;
use App\DataProvider\QuestionDataProvider;

class FacadeQuestion implements FacadeQuestionInterface
{
    public function __construct(
        private QuestionRepositoryInterface                                   $questionRepository,
        private FindByExamSlugMappedAsArrayInterface                          $findByExamSlugMappedAsArray,
        private GetCurrentQuestionAndCreateUserQuestionMappedAsArrayInterface $getCurrentQuestionAndCreateUserQuestion,
        private FindOneByExamAndQuestionSlugInterface                         $findOneByExamAndQuestionSlug,
    )
    {
    }

    public function findByExamSlug(string $slug): array
    {
        return $this->questionRepository->findByExamSlug($slug);
    }

    public function findByExamSlugMappedAsArray(string $examSlug): array
    {
        return $this->findByExamSlugMappedAsArray->find($examSlug);
    }

    public function getCurrentQuestionAndCreateUserQuestionMappedAsArray(array $questionDataProviderList, string $examSlug, int $userId): array
    {
        return $this->getCurrentQuestionAndCreateUserQuestion->get($questionDataProviderList, $examSlug, $userId);
    }

    public function findOneByExamAndQuestionSlug(string $examSlug, string $questionSlug): array
    {
        return $this->findOneByExamAndQuestionSlug->find($examSlug, $questionSlug);
    }
}
