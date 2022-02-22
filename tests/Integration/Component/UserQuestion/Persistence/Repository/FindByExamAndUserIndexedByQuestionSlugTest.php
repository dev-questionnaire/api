<?php
declare(strict_types=1);

namespace App\Tests\Integration\Component\UserQuestion\Persistence\Repository;

use App\Component\UserQuestion\Persistence\Repository\UserQuestionRepositoryInterface;
use App\DataFixtures\UserFixtures;
use App\DataFixtures\UserQuestionFixtures;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FindByExamAndUserIndexedByQuestionSlugTest extends KernelTestCase
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

    public function testFindByExamAndUserIndexedByQuestionSlug(): void
    {
        $userQuestionList = $this->userQuestionRepository->findByExamAndUserIndexedByQuestionSlug('solid', 1);

        self::assertCount(2, $userQuestionList);
        self::assertSame('s_in_solid', $userQuestionList['s_in_solid']->getQuestionSlug());
        self::assertSame('o_in_solid', $userQuestionList['o_in_solid']->getQuestionSlug());
    }

    public function testFindByExamAndUserIndexedByQuestionSlugNegativ(): void
    {
        $userQuestionList = $this->userQuestionRepository->findByExamAndUserIndexedByQuestionSlug('test123', 100);
        self::assertEmpty($userQuestionList);

        $userQuestionList = $this->userQuestionRepository->findByExamAndUserIndexedByQuestionSlug('solid', 100);
        self::assertEmpty($userQuestionList);

        $userQuestionList = $this->userQuestionRepository->findByExamAndUserIndexedByQuestionSlug('test123', 1);
        self::assertEmpty($userQuestionList);

        $userQuestionList = $this->userQuestionRepository->findByExamAndUserIndexedByQuestionSlug('', 0);
        self::assertEmpty($userQuestionList);
    }
}