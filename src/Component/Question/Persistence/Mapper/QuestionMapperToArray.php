<?php

declare(strict_types=1);

namespace App\Component\Question\Persistence\Mapper;

use App\DataProvider\QuestionDataProvider;

class QuestionMapperToArray
{
    /**
     * @return array{answers: array<array-key, mixed>, question: string, slug: string}
     */
    public function map(QuestionDataProvider $questionDataProvider): array
    {
        $array = [];

        $array['slug'] = $questionDataProvider->getSlug();
        $array['question'] = $questionDataProvider->getQuestion();
        $array['answers'] = $questionDataProvider->getAnswers();

        return $array;
    }
}
