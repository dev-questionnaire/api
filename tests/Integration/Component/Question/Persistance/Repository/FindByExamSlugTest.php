<?php
declare(strict_types=1);

namespace App\Tests\Integration\Component\Question\Persistance\Repository;

use App\Component\Question\Persistence\Repository\QuestionRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FindByExamSlugTest extends KernelTestCase
{
    private QuestionRepositoryInterface $questionRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $container = static::getContainer();

        $this->questionRepository = $container->get(QuestionRepositoryInterface::class);
    }

    public function testFindByExamPositiv(): void
    {
        $questionDataProviderList = $this->questionRepository->findByExamSlug('solid');

        self::assertCount(2, $questionDataProviderList);

        self::assertSame('s_in_solid', $questionDataProviderList[0]->getSlug());
        self::assertSame('What does S in SOLID mean?', $questionDataProviderList[0]->getQuestion());
        self::assertSame(["Single possibility"], $questionDataProviderList[0]->getRightAnswers());


        $answers = [
            "Single possibility",
            "Single like a pringle",
            "Solid",
            "Sexy Programming"
        ];

        self::assertSame($answers, $questionDataProviderList[0]->getAnswers());

        self::assertSame('o_in_solid', $questionDataProviderList[1]->getSlug());
        self::assertSame('What does O in SOLID mean?', $questionDataProviderList[1]->getQuestion());
        self::assertSame(["Open close"], $questionDataProviderList[1]->getRightAnswers());


        $answers = [
            "Open Relation",
            "Oral _ex",
            "Open close",
            "Opfer"
        ];

        self::assertSame($answers, $questionDataProviderList[1]->getAnswers());
    }

    public function testFindByExamSlugNegativExamNotFound(): void
    {
        $questionDataProviderList = $this->questionRepository->findByExamSlug('test');

        self::assertEmpty($questionDataProviderList);
    }

    public function testFindByExamSlugNegativExamEmpty(): void
    {
        $questionDataProviderList = $this->questionRepository->findByExamSlug('');

        self::assertEmpty($questionDataProviderList);
    }
}