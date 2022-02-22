<?php
declare(strict_types=1);

namespace App\Tests\Acceptance\Component\Question\Controller;

use App\DataFixtures\UserFixtures;
use App\DataFixtures\UserQuestionFixtures;
use App\Repository\UserQuestionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GetCurrentQuestionTest extends WebTestCase
{
    protected EntityManagerInterface $entityManager;
    protected string $apiUrl;
    protected KernelBrowser $client;
    protected UserRepository $userRepository;
    protected UserQuestionRepository $userQuestionRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $container = self::getContainer();

        $this->entityManager = self::getContainer()
            ->get('doctrine')
            ->getManager();

        $this->apiUrl = $container->get(ParameterBagInterface::class)->get('api.url');

        $this->userRepository = $container->get(UserRepository::class);
        $this->userQuestionRepository = $container->get(UserQuestionRepository::class);

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

    public function testGetCurrentQuestionNotLoggedIn(): void
    {
        $this->client->jsonRequest('GET',
            $this->apiUrl . '/api/question/getCurrentQuestion', [
                'token' => null,
            ]);


        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertResponseStatusCodeSame(401);
        self::assertSame('access not authorized', $content['message']);
    }

    public function testGetCurrentQuestionUserQuestionExistsLoggedIn(): void
    {
        $this->client->jsonRequest('POST',
            $this->apiUrl . '/api/login', [
                'username' => 'user@cec.valantic.com',
                'password' => 'user',
            ]
        );

        self::assertCount(1, $this->userQuestionRepository->findBy(['examSlug' => 'harun']));
        $user = $this->userRepository->find(1);

        $this->client->jsonRequest('GET',
            $this->apiUrl . '/api/question/getCurrentQuestion', [
                'token' => $user->getToken(),
                'examSlug' => 'harun',
            ]);


        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertResponseIsSuccessful();
        self::assertCount(1, $this->userQuestionRepository->findBy(['examSlug' => 'harun']));

        $question = $content['question'];

        self::assertSame('harun_alter', $question['slug']);
    }

    public function testGetCurrentQuestionCreateUserQuestionLoggedIn(): void
    {
        $this->client->jsonRequest('POST',
            $this->apiUrl . '/api/login', [
                'username' => 'user@cec.valantic.com',
                'password' => 'user',
            ]
        );

        $userQuestionList = $this->userQuestionRepository->findBy(['examSlug' => 'harun']);
        self::assertCount(1, $userQuestionList);

        $userQuestionList[0]->setAnswers([
            '19' => true,
            '22' => false,
            '21' => false,
            '20' => false,
        ]);

        $this->entityManager->flush();

        $user = $this->userRepository->find(1);

        $this->client->jsonRequest('GET',
            $this->apiUrl . '/api/question/getCurrentQuestion', [
                'token' => $user->getToken(),
                'examSlug' => 'harun',
            ]);


        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertResponseIsSuccessful();
        self::assertCount(2, $this->userQuestionRepository->findBy(['examSlug' => 'harun']));

        $question = $content['question'];

        self::assertSame('harun_fuss', $question['slug']);
    }

    public function testGetCurrentQuestionLoggedInNegativ(): void
    {
        $this->client->jsonRequest('POST',
            $this->apiUrl . '/api/login', [
                'username' => 'user@cec.valantic.com',
                'password' => 'user',
            ]
        );

        $user = $this->userRepository->find(1);

        $this->client->jsonRequest('GET',
            $this->apiUrl . '/api/question/getCurrentQuestion', [
                'token' => $user->getToken(),
                'examSlug' => 'test123',
            ]);


        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertResponseIsSuccessful();

        self::assertSame('Question not found', $content['error']);
    }
}