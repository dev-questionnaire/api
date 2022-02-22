<?php
declare(strict_types=1);

namespace App\Tests\Integration\Component\Question\Persistance\Repository;

use App\Component\Question\Persistence\Repository\QuestionRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FindOneByExamAndQuestionSlugTest extends KernelTestCase
{
    private QuestionRepositoryInterface $questionRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $container = static::getContainer();

        $this->questionRepository = $container->get(QuestionRepositoryInterface::class);
    }

    public function testFindByExamAndQuestionPositiv(): void
    {
        $questionDataProvider = $this->questionRepository->findOneByExamAndQuestionSlug('solid', 's_in_solid');


        self::assertSame('s_in_solid', $questionDataProvider->getSlug());
        self::assertSame('What does S in SOLID mean?', $questionDataProvider->getQuestion());
        self::assertSame(["Single possibility"], $questionDataProvider->getRightAnswers());


        $answers = [
            "Single possibility",
            "Single like a pringle",
            "Solid",
            "Sexy Programming"
        ];

        self::assertSame($answers, $questionDataProvider->getAnswers());
    }

    public function testFindByExamAndQuestionNegativEmpty(): void
    {
        $questionDataProvider = $this->questionRepository->findOneByExamAndQuestionSlug('', '');

        self::assertNull($questionDataProvider);
    }

    public function testFindByExamAndQuestionNegativQuestionNotFound(): void
    {
        $questionDataProvider = $this->questionRepository->findOneByExamAndQuestionSlug('solid', 'test123');

        self::assertNull($questionDataProvider);
    }

    public function testFindByExamAndQuestionNegativExamNotFound(): void
    {
        $questionDataProvider = $this->questionRepository->findOneByExamAndQuestionSlug('test123', 'test123');

        self::assertNull($questionDataProvider);
    }
}