<?php

declare(strict_types=1);

namespace App\Component\Exam\Dependency;

interface BridgeQuestionInterface
{
    /**
     * @return array<array-key, \App\DataProvider\QuestionDataProvider>
     */
    public function getByExamSlug(string $exam): array;
}
