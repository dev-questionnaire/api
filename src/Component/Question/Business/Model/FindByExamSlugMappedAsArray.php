<?php
declare(strict_types=1);

namespace App\Component\Question\Business\Model;

use App\Component\Question\Persistence\Mapper\QuestionMapperToArray;
use App\Component\Question\Persistence\Repository\QuestionRepositoryInterface;

class FindByExamSlugMappedAsArray implements FindByExamSlugMappedAsArrayInterface
{
    public function __construct(
        private QuestionRepositoryInterface $questionRepository,
        private QuestionMapperToArray $questionMapperToArray,
    )
    {
    }

    public function find(string $examSlug): array
    {
        $questionDataProviderList = $this->questionRepository->findByExamSlug($examSlug);

        $questionList = [];

        foreach ($questionDataProviderList as $questionDataProvider) {
            $questionList[] = $this->questionMapperToArray->map($questionDataProvider);
        }

        return $questionList;
    }
}