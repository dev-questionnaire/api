<?php

declare(strict_types=1);

namespace App\Component\Question\Persistence\Repository;

use App\DataProvider\QuestionDataProvider;

interface QuestionRepositoryInterface
{
    /**
     * @return QuestionDataProvider[]
     */
    public function findByExamSlug(string $examSlug): array;

    public function findOneByExamAndQuestionSlug(string $examSlug, string $questionSlug): ?QuestionDataProvider;
}