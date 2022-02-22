<?php

declare(strict_types=1);

namespace App\Component\Exam\Persistence\Mapper;

use App\DataProvider\ExamDataProvider;

/**
 * @psalm-suppress MixedAssignment
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress MixedArgument
 */
class ExamMapperToDataProvider
{
    /**
     * @throws \JsonException
     */
    public function map(string $path): ExamDataProvider
    {
        if(!file_exists($path)) {
            throw new \RuntimeException("File not found");
        }

        /** @var string $fileContent */
        $fileContent = file_get_contents($path);

        /** @var array<array-key, string> */
        $exam = json_decode($fileContent, true, 512, JSON_THROW_ON_ERROR);

        $name = $exam['exam'];

        $slug = $exam['slug'];

        $examDataProvider = new ExamDataProvider();
        $examDataProvider
            ->setName($name)
            ->setSlug($slug);

        return $examDataProvider;
    }
}
