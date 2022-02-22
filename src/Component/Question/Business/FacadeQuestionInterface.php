<?php

declare(strict_types=1);

namespace App\Component\Question\Business;

interface FacadeQuestionInterface
{
    /**
     * @return \App\DataProvider\QuestionDataProvider[]
     */
    public function findByExamSlug(string $slug): array;

    public function findByExamSlugMappedAsArray(string $examSlug): array;

    public function getCurrentQuestionAndCreateUserQuestionMappedAsArray(array $questionDataProviderList, string $examSlug, int $userId): array;

    /**
     * @return array{answers: array<array-key, mixed>, question: string, slug: string}
     */
    public function findOneByExamAndQuestionSlug(string $examSlug, string $questionSlug): array;
}
