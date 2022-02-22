<?php
declare(strict_types=1);

namespace App\Tests\Acceptance\Component\Exam;

use App\DataFixtures\UserFixtures;
use App\DataFixtures\UserQuestionFixtures;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ExamControllerResultTest extends WebTestCase
{
    protected EntityManagerInterface $entityManager;
    protected string $apiUrl;
    protected KernelBrowser $client;
    protected UserRepository $userRepository;

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

    public function testResultNotLoggedIn(): void
    {
        $this->client->jsonRequest('GET',
            $this->apiUrl . '/api/exam/result', [
                'token' => null,
            ]);


        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertResponseStatusCodeSame(401);
        self::assertSame('access not authorized', $content['message']);
    }

    public function testResultLoggedInNegativNotAnswered(): void
    {
        $this->client->jsonRequest('POST',
            $this->apiUrl . '/api/login', [
                'username' => 'user@cec.valantic.com',
                'password' => 'user',
            ]
        );

        $user = $this->userRepository->find(1);

        $this->client->jsonRequest('GET',
            $this->apiUrl . '/api/exam/result', [
                'examSlug' => 'harun',
                'token' => $user->getToken(),
            ]);


        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertResponseIsSuccessful();

        self::assertSame('not answered any questions', $content['error']);
    }

    public function testResultLoggedInNegativNotAnsweredAll(): void
    {
        $this->client->jsonRequest('POST',
            $this->apiUrl . '/api/login', [
                'username' => 'admin@cec.valantic.com',
                'password' => 'admin',
            ]
        );

        $user = $this->userRepository->find(2);

        $this->client->jsonRequest('GET',
            $this->apiUrl . '/api/exam/result', [
                'examSlug' => 'solid',
                'token' => $user->getToken(),
            ]);


        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertResponseIsSuccessful();

        self::assertSame('not all questions were answerd', $content['error']);
    }

    public function testResultLoggedInNegativExamNotFound(): void
    {
        $this->client->jsonRequest('POST',
            $this->apiUrl . '/api/login', [
                'username' => 'admin@cec.valantic.com',
                'password' => 'admin',
            ]
        );

        $user = $this->userRepository->find(2);

        $this->client->jsonRequest('GET',
            $this->apiUrl . '/api/exam/result', [
                'examSlug' => 'test123',
                'token' => $user->getToken(),
            ]);


        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertResponseIsSuccessful();

        self::assertSame('exam not found', $content['error']);
    }

    public function testResultLoggedInPositivUser(): void
    {
        $this->client->jsonRequest('POST',
            $this->apiUrl . '/api/login', [
                'username' => 'user@cec.valantic.com',
                'password' => 'user',
            ]
        );

        $user = $this->userRepository->find(1);

        $this->client->jsonRequest('GET',
            $this->apiUrl . '/api/exam/result', [
                'examSlug' => 'solid',
                'token' => $user->getToken(),
            ]);


        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertResponseIsSuccessful();
        self::assertSame(50, $content['examPercent']);
        self::assertTrue($content['answeredCorrect']['s_in_solid']);
        self::assertFalse($content['answeredCorrect']['o_in_solid']);

        self::assertSame('Single possibility', $content['userAnswers']['s_in_solid'][0]);
        self::assertSame('Open close', $content['userAnswers']['o_in_solid'][0]);
        self::assertSame('Open relation', $content['userAnswers']['o_in_solid'][1]);
    }

    public function testResultLoggedInPositivAdmin(): void
    {
        $this->client->jsonRequest('POST',
            $this->apiUrl . '/api/login', [
                'username' => 'admin@cec.valantic.com',
                'password' => 'admin',
            ]
        );

        $user = $this->userRepository->find(2);

        $this->client->jsonRequest('GET',
            $this->apiUrl . '/api/exam/result', [
                'examSlug' => 'harun',
                'token' => $user->getToken(),
                'role' => 'ROLE_ADMIN',
                'id' => 1,
            ]);


        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertResponseIsSuccessful();
        self::assertSame(0, $content['examPercent']);

        self::assertNull($content['answeredCorrect']['harun_alter']);
        self::assertNull($content['answeredCorrect']['harun_fuss']);
        self::assertNull($content['answeredCorrect']['harun_beziehungen']);
        self::assertNull($content['answeredCorrect']['harun_friseur']);
        self::assertNull($content['answeredCorrect']['harun_frauen']);

        self::assertNull($content['userAnswers']['harun_alter']);
        self::assertNull($content['userAnswers']['harun_fuss']);
        self::assertNull($content['userAnswers']['harun_beziehungen']);
        self::assertNull($content['userAnswers']['harun_friseur']);
        self::assertNull($content['userAnswers']['harun_frauen']);
    }
}