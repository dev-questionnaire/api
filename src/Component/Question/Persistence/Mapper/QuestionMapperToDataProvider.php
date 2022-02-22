<?php

declare(strict_types=1);

namespace App\Component\Question\Persistence\Mapper;

use App\DataProvider\QuestionDataProvider;

class QuestionMapperToDataProvider
{
    /**
     * @throws \JsonException
     */
    public function map(string $path): QuestionDataProvider
    {
        if(!file_exists($path)) {
            throw new \RuntimeException("File not found");
        }

        /** @var string $fileContent */
        $fileContent = file_get_contents($path);

        /** @var array<array-key, string|array<array-key, string>> */
        $question = json_decode($fileContent, true, 512, JSON_THROW_ON_ERROR);

        /** @var string */
        $questionName = $question['question'];
        /** @var array<array-key, string> */
        $rightAnswers = $question['right_answer'];
        /** @var array<array-key, string> */
        $answers = $question['answer'];
        /** @var string */
        $slug = $question['slug'];

        $questionDataProvider = new QuestionDataProvider();
        $questionDataProvider
            ->setQuestion($questionName)
            ->setRightAnswers($rightAnswers)
            ->setAnswers($answers)
            ->setSlug($slug);

        return $questionDataProvider;
    }
}
