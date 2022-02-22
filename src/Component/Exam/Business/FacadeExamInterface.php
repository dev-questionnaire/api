<?php

declare(strict_types=1);

namespace App\Component\Exam\Business;

use App\DataProvider\ExamDataProvider;

interface FacadeExamInterface
{
    public function findBySlug(string $slug): ?ExamDataProvider;

    /**
     * @return array<array-key, array<array-key, int|string|null>>
     */
    public function getAllFormattedAsArray(): array;

    /**
     * @return array{name: string, slug: string}
     */
    public function findBySlugFormattedAsArray(string $slug): array;
}
