<?php
declare(strict_types=1);

namespace App\Component\Question\Business\Model;

interface GetCurrentQuestionAndCreateUserQuestionMappedAsArrayInterface
{
    /**
     * @param \App\DataProvider\QuestionDataProvider[]
     * @return array{answers: array<array-key, mixed>, question: string, slug: string}
     */
    public function get(array $questionDataProviderList, string $examSlug, int $userId): array;
}