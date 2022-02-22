<?php

declare(strict_types=1);

namespace App\Component\Exam\Persistence\Repository;

use App\DataProvider\ExamDataProvider;

interface ExamRepositoryInterface
{
    public function findBySlug(string $slug): ?ExamDataProvider;

    /**
     * @return ExamDataProvider[]
     */
    public function getAll(): array;
}
