<?php
declare(strict_types=1);

namespace App\Tests\Integration\Component\Exam\Dependency;

use App\Component\Exam\Dependency\BridgeUserQuestion;
use App\Component\Exam\Dependency\BridgeUserQuestionInterface;
use App\Component\Question\Persistence\Repository\QuestionRepository;
use App\Component\UserQuestion\Business\FacadeUserQuestion;
use App\Component\UserQuestion\Persistence\Repository\UserQuestionRepository;
use App\DataFixtures\UserFixtures;
use App\DataFixtures\UserQuestionFixtures;
use App\DataProvider\UserQuestionDataProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BridgeUserQuestionTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;
    private ?BridgeUserQuestionInterface $bridgeUserQuestion;
    private ContainerInterface $container;

    protected function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();
        $this->container = static::getContainer();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->bridgeUserQuestion = $this->container->get(BridgeUserQuestion::class);

        $this->container->get(UserFixtures::class)->load($this->entityManager);
        $this->container->get(UserQuestionFixtures::class)->load($this->entityManager);
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

        $this->bridgeUserQuestion = null;
        $this->entityManager = null;
    }

    public function testGetByUserAndExamIndexedByQuestionSlug(): void
    {
        $userQuestionDataProviderList = $this->bridgeUserQuestion->findByUserAndExamIndexedByQuestionSlug(1, 'solid');
        self::assertCount(2, $userQuestionDataProviderList);

        self::assertInstanceOf(UserQuestionDataProvider::class, $userQuestionDataProviderList['s_in_solid']);

        self::assertEmpty($this->bridgeUserQuestion->findByUserAndExamIndexedByQuestionSlug(1, ''));
    }

    public function testGetPercentAndAnswerCorrectAndUserAnswerListAdmin(): void
    {
        $userQuestion = $this->container->get(\App\Repository\UserQuestionRepository::class)->findOneBy(['questionSlug' => 'o_in_solid', 'user' => 1]);
        $userQuestion->setAnswers(null);
        $this->entityManager->flush();

        $questionDTOList = $this->container->get(QuestionRepository::class)->findByExamSlug('solid');
        $userQuestionDTOList = $this->container->get(UserQuestionRepository::class)->findByExamAndUserIndexedByQuestionSlug('solid', 1);


        $results = $this->bridgeUserQuestion->getPercentAndAnswerCorrectAndUserAnswerList($questionDTOList, $userQuestionDTOList, true);

        self::assertNotEmpty($results);
    }

    public function testGetPercentAndAnswerCorrectAndUserAnswerList(): void
    {
        $questionDTOList = $this->container->get(QuestionRepository::class)->findByExamSlug('solid');
        $userQuestionDTOList = $this->container->get(UserQuestionRepository::class)->findByExamAndUserIndexedByQuestionSlug('solid', 1);

        $results = $this->bridgeUserQuestion->getPercentAndAnswerCorrectAndUserAnswerList($questionDTOList, $userQuestionDTOList)['answeredCorrect'];

        self::assertTrue($results['s_in_solid']);
        self::assertFalse($results['o_in_solid']);
    }
}