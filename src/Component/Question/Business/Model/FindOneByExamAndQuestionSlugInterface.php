<?php
declare(strict_types=1);

namespace App\Component\Question\Business\Model;

interface FindOneByExamAndQuestionSlugInterface
{
    /**
     * @return array{answers: array<array-key, mixed>, question: string, slug: string}
     */
    public function find(string $examSlug, string $questionSlug): array;
}