<?php

declare(strict_types=1);

namespace App\Component\Exam\Dependency;

use App\Component\Question\Business\FacadeQuestionInterface;

class BridgeQuestion implements BridgeQuestionInterface
{
    public function __construct(
        private FacadeQuestionInterface $facadeQuestion,
    ) {
    }

    public function getByExamSlug(string $exam): array
    {
        return $this->facadeQuestion->findByExamSlug($exam);
    }
}
