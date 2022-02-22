<?php

declare(strict_types=1);

namespace App\Component\Exam\Persistence\Repository;

use App\Component\Exam\Persistence\Mapper\ExamMapperToDataProvider;
use App\DataProvider\ExamDataProvider;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;

class ExamRepository implements ExamRepositoryInterface
{
    private string $pathToFolder;

    public function __construct(
        private ExamMapperToDataProvider $examMapper,
        ParameterBagInterface            $params
    ) {
        /** @var string $path */
        $path = $params->get('app_content_folder');

        $this->pathToFolder = $path;
    }

    public function findBySlug(string $slug): ?ExamDataProvider
    {
        if (empty($slug)) {
            return null;
        }

        $finder = new Finder();

        $path = "{$this->pathToFolder}/*/";

        $fileList = $finder
            ->in($path)
            ->name('index.json')
            ->sortByName()
            ->files()->contains(['slug' => $slug]);

        /** @var null|ExamDataProvider $examDataProvider */
        $examDataProvider = null;

        foreach ($fileList as $file) {
            $examDataProvider = $this->examMapper->map($file->getPathname());
        }

        return $examDataProvider;
    }

    /**
     * @return ExamDataProvider[]
     * @throws \JsonException
     */
    public function getAll(): array
    {
        $examDataProviderList = [];

        $finder = new Finder();

        $path = "{$this->pathToFolder}/*/";

        $fileList = $finder
            ->in($path)
            ->name('index.json')
            ->sortByName();

        foreach ($fileList as $file) {
            $examDataProviderList[] = $this->examMapper->map($file->getPathname());
        }

        return $examDataProviderList;
    }
}
