<?php
declare(strict_types=1);

namespace App\Tests\Integration\Component\Exam\Dependency;

use App\Component\Exam\Dependency\BridgeQuestion;
use App\Component\Exam\Dependency\BridgeQuestionInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BridgeQuestionTest extends KernelTestCase
{
    private BridgeQuestionInterface $bridgeQuestion;

    protected function setUp(): void
    {
        parent::setUp();

        $container = static::getContainer();

        $this->bridgeQuestion = $container->get(BridgeQuestion::class);
    }

    public function testGetByExam(): void
    {
        $questionList = $this->bridgeQuestion->getByExamSlug('solid');
        self::assertCount(2, $questionList);

        $questionList = $this->bridgeQuestion->getByExamSlug('');
        self::assertEmpty($questionList);
    }
}