<?php
declare(strict_types=1);

namespace App\Tests\Integration\Component\UserQuestion\Persistence\Repository;

use App\Component\UserQuestion\Persistence\Repository\UserQuestionRepositoryInterface;
use App\DataFixtures\UserFixtures;
use App\DataFixtures\UserQuestionFixtures;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class findOneByQuestionAndUserTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private UserQuestionRepositoryInterface $userQuestionRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $container = self::getContainer();

        $this->entityManager = self::getContainer()
            ->get('doctrine')
            ->getManager();

        $this->userQuestionRepository = $container->get(UserQuestionRepositoryInterface::class);

        $container->get(UserFixtures::class)->load($this->entityManager);
        $container->get(UserQuestionFixtures::class)->load($this->entityManager);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $connection = $this->entityManager->getConnection();

        $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 0');
        $connection->executeQuery('DELETE FROM user_question');
        $connection->executeQuery('ALTER TABLE user_question AUTO_INCREMENT=0');
        $connection->executeQuery('DELETE FROM user');
        $connection->executeQuery('ALTER TABLE user AUTO_INCREMENT=0');
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 1');

        $connection->close();
    }

    public function testFindOneByQuestionAndUser(): void
    {
        $userQuestion = $this->userQuestionRepository->findOneByQuestionAndUser('s_in_solid', 1);

        $answers = ["Solid" => false, "Sexy_Programming" => false, "Single_possibility" => true, "Single_like_a_pringle" => false];

        self::assertSame($answers, $userQuestion->getAnswers());
        self::assertSame('solid', $userQuestion->getExamSlug());
    }

    public function testFindOneByQuestionAndUserNegativ(): void
    {
        $userQuestion = $this->userQuestionRepository->findOneByQuestionAndUser('test123', 100);
        self::assertNull($userQuestion);

        $userQuestion = $this->userQuestionRepository->findOneByQuestionAndUser('s_in_solid', 100);
        self::assertNull($userQuestion);

        $userQuestion = $this->userQuestionRepository->findOneByQuestionAndUser('test123', 1);
        self::assertNull($userQuestion);

        $userQuestion = $this->userQuestionRepository->findOneByQuestionAndUser('', 0);
        self::assertNull($userQuestion);
    }
}