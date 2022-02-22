<?php
declare(strict_types=1);

namespace App\Tests\Integration\Component\Exam\Persistance\Repository;

use App\Component\Exam\Persistence\Repository\ExamRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RepositoryTest extends KernelTestCase
{
    private ExamRepositoryInterface $examRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $container = static::getContainer();

        $this->examRepository = $container->get(ExamRepositoryInterface::class);
    }

    public function testGetAll(): void
    {
        $examDataProviderList = $this->examRepository->getAll();

        self::assertCount(4, $examDataProviderList);
        self::assertSame('Haruns geiles Quiz', $examDataProviderList[0]->getName());
        self::assertSame('OOP', $examDataProviderList[1]->getName());
        self::assertSame('SOLID', $examDataProviderList[2]->getName());
        self::assertSame('Testing', $examDataProviderList[3]->getName());

        self::assertSame('harun', $examDataProviderList[0]->getSlug());
        self::assertSame('oop', $examDataProviderList[1]->getSlug());
        self::assertSame('solid', $examDataProviderList[2]->getSlug());
        self::assertSame('testing', $examDataProviderList[3]->getSlug());
    }

    public function testGetByNamePositiv(): void
    {
        $examDataProvider = $this->examRepository->findBySlug('oop');

        self::assertSame('OOP', $examDataProvider->getName());
    }

    public function testGetByNameNegativ(): void
    {
        $result = $this->examRepository->findBySlug('does not exist');

        self::assertNull($result);

        $result = $this->examRepository->findBySlug('');

        self::assertNull($result);
    }
}