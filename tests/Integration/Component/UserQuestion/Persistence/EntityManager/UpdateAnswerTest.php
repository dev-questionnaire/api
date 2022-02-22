<?php
declare(strict_types=1);

namespace App\Tests\Integration\Component\UserQuestion\Persistence\EntityManager;

use App\Component\UserQuestion\Persistence\EntityManager\UserQuestionEntityManagerInterface;
use App\Component\UserQuestion\Persistence\Repository\UserQuestionRepositoryInterface;
use App\DataFixtures\UserFixtures;
use App\DataFixtures\UserQuestionFixtures;
use App\DataProvider\UserQuestionDataProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UpdateAnswerTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private UserQuestionRepositoryInterface $userQuestionRepository;
    private UserQuestionEntityManagerInterface $userQuestionEntityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $container = self::getContainer();

        $this->entityManager = self::getContainer()
            ->get('doctrine')
            ->getManager();

        $this->userQuestionRepository = $container->get(UserQuestionRepositoryInterface::class);
        $this->userQuestionEntityManager = $container->get(UserQuestionEntityManagerInterface::class);

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

    public function testUpdateAnswer(): void
    {
        $userQuestionDataProvider = new UserQuestionDataProvider();
        $userQuestionDataProvider
            ->setAnswers(null)
            ->setQuestionSlug('o_in_solid')
            ->setExamSlug('solid')
            ->setUserId(2);

        $this->userQuestionEntityManager->create($userQuestionDataProvider);
        $userQuestionDataProvider = $this->userQuestionRepository->findOneByQuestionAndUser('o_in_solid', 2);
        self::assertNull($userQuestionDataProvider->getAnswers());

        $userQuestionDataProvider
            ->setAnswers(['Open_relation' => true, 'Oral__ex' => false, 'Open_close' => true, 'Opfer' => false]);

        $this->userQuestionEntityManager->updateAnswer($userQuestionDataProvider);

        $userQuestionDataProvider = $this->userQuestionRepository->findOneByQuestionAndUser('o_in_solid', 2);

        self::assertSame(['Open_relation' => true, 'Oral__ex' => false, 'Open_close' => true, 'Opfer' => false],
            $userQuestionDataProvider->getAnswers());
    }

    public function testUpdateAnswerNegativ(): void
    {
        $userQuestionDataProvider = new UserQuestionDataProvider();
        $userQuestionDataProvider->setId(100);

        $this->expectExceptionMessage("UserQuestion not found");

        $this->userQuestionEntityManager->updateAnswer($userQuestionDataProvider);
    }
}