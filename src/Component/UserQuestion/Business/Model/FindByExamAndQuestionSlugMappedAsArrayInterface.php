<?php
declare(strict_types=1);

namespace App\Component\UserQuestion\Business\Model;

interface FindByExamAndQuestionSlugMappedAsArrayInterface
{
    /**
     * @return list<array{userId: int, answers: array<array-key, mixed>}>
     */
    public function find(string $exam, string $question): array;
}