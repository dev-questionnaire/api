<?php

declare(strict_types=1);

namespace App\Component\Exam\Business;

use App\Component\Exam\Business\Model\FindBySlugMappedAsArrayInterface;
use App\Component\Exam\Business\Model\GetAllMappedAsArray;
use App\Component\Exam\Business\Model\GetAllMappedAsArrayInterface;
use App\Component\Exam\Persistence\Repository\ExamRepositoryInterface;
use App\DataProvider\ExamDataProvider;

class FacadeExam implements FacadeExamInterface
{
    public function __construct(
        private ExamRepositoryInterface      $examRepository,
        private GetAllMappedAsArrayInterface $getAllFormattedAsArray,
        private FindBySlugMappedAsArrayInterface $findBySlugMappedAsArray,
    ) {
    }

    public function findBySlug(string $slug): ?ExamDataProvider
    {
        return $this->examRepository->findBySlug($slug);
    }

    public function getAllFormattedAsArray(): array
    {
        return $this->getAllFormattedAsArray->getAll();
    }

    public function findBySlugFormattedAsArray(string $slug): array
    {
        return $this->findBySlugMappedAsArray->find($slug);
    }
}
