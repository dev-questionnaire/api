<?php
declare(strict_types=1);

namespace App\Component\Exam\Business\Model;

interface GetAllMappedAsArrayInterface
{
    /**
     * @return array<array-key, array<array-key, int|string|null>>
     */
    public function getAll(): array;
}