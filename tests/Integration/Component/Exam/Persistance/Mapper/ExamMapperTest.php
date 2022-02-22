<?php
declare(strict_types=1);

namespace App\Tests\Integration\Component\Exam\Persistance\Mapper;

use App\Component\Exam\Persistence\Mapper\ExamMapperToDataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ExamMapperTest extends KernelTestCase
{
    public function testMapNegativ(): void
    {
        $container = static::getContainer();
        $mapper = $container->get(ExamMapperToDataProvider::class);

        $this->expectExceptionMessage('File not found');
        $mapper->map('path that does no exist');
    }
}