<?php

declare(strict_types=1);

namespace App\Component\Question\Persistence\Repository;

use App\Component\Question\Persistence\Mapper\QuestionMapperToDataProvider;
use App\DataProvider\QuestionDataProvider;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;

class QuestionRepository implements QuestionRepositoryInterface
{
    private string $pathToFolder;

    public function __construct(
        private QuestionMapperToDataProvider $questionMapper,
        ParameterBagInterface                $params
    ) {
        /** @var string $path */
        $path = $params->get('app_content_folder');
        $this->pathToFolder = $path;
    }

    public function findByExamSlug(string $examSlug): array
    {
        $questionDataProviderList = [];

        if (empty($examSlug)) {
            return [];
        }

        $finder = new Finder();

        $path = "{$this->pathToFolder}/{$examSlug}/";

        if (!file_exists($path . "index.json")) {
            return [];
        }

        $fileList = $finder
            ->in($path)
            ->name('*.json')
            ->sortByName()
            ->files()->contains('question');

        foreach ($fileList as $file) {
            $questionDataProviderList[] = $this->questionMapper->map($file->getPathname());
        }

        return $questionDataProviderList;
    }

    public function findOneByExamAndQuestionSlug(string $examSlug, string $questionSlug): ?QuestionDataProvider
    {
        if (empty($examSlug) || empty($questionSlug)) {
            return null;
        }

        $finder = new Finder();

        $path = "{$this->pathToFolder}/{$examSlug}/";

        if (!file_exists($path . "index.json")) {
            return null;
        }

        $fileList = $finder
            ->in($path)
            ->name('*.json')
            ->sortByName()
            ->files()->contains($questionSlug);

        /** @var null|QuestionDataProvider $questionDataProvider */
        $questionDataProvider = null;

        foreach ($fileList as $file) {
            $questionDataProvider = $this->questionMapper->map($file->getPathname());
        }

        return $questionDataProvider;
    }
}
