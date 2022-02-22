<?php
declare(strict_types=1);

namespace App\Component\Exam\Business\Model;

interface FindBySlugMappedAsArrayInterface
{
    /**
     * @return array{name: string, slug: string}
     */
    public function find(string $slug): array;
}