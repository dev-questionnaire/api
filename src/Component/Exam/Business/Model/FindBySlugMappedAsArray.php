<?php
declare(strict_types=1);

namespace App\Component\Exam\Business\Model;

use App\Component\Exam\Persistence\Mapper\ExamMapperToArray;
use App\Component\Exam\Persistence\Repository\ExamRepositoryInterface;
use App\DataProvider\ExamDataProvider;

class FindBySlugMappedAsArray implements FindBySlugMappedAsArrayInterface
{
    public function __construct(
        private ExamRepositoryInterface $examRepository,
        protected ExamMapperToArray $examMapperToArray,
    ) {
    }

    public function find(string $slug): array
    {
        $examDataProvider = $this->examRepository->findBySlug($slug);

        if(!$examDataProvider instanceof ExamDataProvider) {
            return [];
        }

        return $this->examMapperToArray->map($examDataProvider);
    }
}