<?php

declare(strict_types=1);

namespace App\Component\Exam\Business\Model;

use App\Component\Exam\Persistence\Mapper\ExamMapperToArray;
use App\Component\Exam\Persistence\Repository\ExamRepositoryInterface;

class GetAllMappedAsArray implements GetAllMappedAsArrayInterface
{
    public function __construct(
        private ExamRepositoryInterface $examRepository,
        protected ExamMapperToArray $examMapperToArray,
    ) {
    }

    public function getAll(): array
    {
        $examDataProviderList = $this->examRepository->getAll();

        $examListJson = [];

        /**
         * @var int $key
         * @var \App\DataProvider\ExamDataProvider $examDataProvider
         */
        foreach ($examDataProviderList as $key => $examDataProvider) {
            $examListJson[$key] = $this->examMapperToArray->map($examDataProvider);
        }

        return $examListJson;
    }
}
