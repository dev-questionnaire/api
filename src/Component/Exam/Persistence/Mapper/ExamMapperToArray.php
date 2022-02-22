<?php

declare(strict_types=1);

namespace App\Component\Exam\Persistence\Mapper;

use App\DataProvider\ExamDataProvider;

class ExamMapperToArray
{
    /**
     * @return array{name: string, slug: string}
     */
    public function map(ExamDataProvider $examDataProvider): array
    {
        $array = [];

        $array['slug'] = $examDataProvider->getSlug();
        $array['name'] = $examDataProvider->getName();

        return $array;
    }
}
