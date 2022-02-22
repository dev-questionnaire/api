<?php
declare(strict_types=1);

namespace App\Tests\Integration\Component\UserQuestion\Persistence\Repository;

use App\Component\UserQuestion\Persistence\Repository\UserQuestionRepositoryInterface;
use App\DataFixtures\UserFixtures;
use App\DataFixtures\UserQuestionFixtures;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FindByExamAndQuestionSlugTest extends KernelTestCase
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

    public function testFindByExamAndQuestionSlug(): void
    {
        $userQuestionList = $this->userQuestionRepository->findByExamAndQuestionSlug('solid', 's_in_solid');

        self::assertCount(2, $userQuestionList);

        self::assertSame(1, $userQuestionList[0]->getUserId());
        self::assertSame(2, $userQuestionList[1]->getUserId());
    }

    public function testFindByExamAndQuestionSlugNegativ(): void
    {
        $userQuestionList = $this->userQuestionRepository->findByExamAndQuestionSlug('test123', 'test123');
        self::assertEmpty($userQuestionList);

        $userQuestionList = $this->userQuestionRepository->findByExamAndQuestionSlug('solid', 'test123');
        self::assertEmpty($userQuestionList);

        $userQuestionList = $this->userQuestionRepository->findByExamAndQuestionSlug('test123', 's_in_solid');
        self::assertEmpty($userQuestionList);

        $userQuestionList = $this->userQuestionRepository->findByExamAndQuestionSlug('', '');
        self::assertEmpty($userQuestionList);
    }
}