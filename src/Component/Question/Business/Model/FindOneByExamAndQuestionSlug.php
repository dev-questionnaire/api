<?php
declare(strict_types=1);

namespace App\Component\Question\Business\Model;

use App\Component\Question\Persistence\Mapper\QuestionMapperToArray;
use App\Component\Question\Persistence\Repository\QuestionRepositoryInterface;
use App\DataProvider\QuestionDataProvider;

class FindOneByExamAndQuestionSlug implements FindOneByExamAndQuestionSlugInterface
{
    public function __construct(
        private QuestionRepositoryInterface $questionRepository,
        private QuestionMapperToArray $questionMapperToArray,
    )
    {
    }

    public function find(string $examSlug, string $questionSlug): array
    {
        $questionDataProvider = $this->questionRepository->findOneByExamAndQuestionSlug($examSlug, $questionSlug);

        if(!$questionDataProvider instanceof QuestionDataProvider) {
            return [];
        }

        return $this->questionMapperToArray->map($questionDataProvider);
    }
}