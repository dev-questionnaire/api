<?php
declare(strict_types=1);

namespace App\Tests\Acceptance\Component\UserQuestion\Controller;

use App\DataFixtures\UserFixtures;
use App\DataFixtures\UserQuestionFixtures;
use App\Repository\UserQuestionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FindByExamAndQuestionSlugTest extends WebTestCase
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

    public function testFindByExamAndQuestionSlugNotLoggedIn(): void
    {
        $this->client->jsonRequest('GET',
            $this->apiUrl . '/api/userQuestion/findByExamAndQuestionSlug', [
                'token' => null,
            ]);


        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertResponseStatusCodeSame(401);
        self::assertSame('access not authorized', $content['message']);
    }

    public function testFindByExamAndQuestionSlugLoggedInPositiv(): void
    {
        $this->client->jsonRequest('POST',
            $this->apiUrl . '/api/login', [
                'username' => 'admin@cec.valantic.com',
                'password' => 'admin',
            ]
        );

        $user = $this->userRepository->find(2);

        $this->client->jsonRequest('GET',
            $this->apiUrl . '/api/userQuestion/findByExamAndQuestionSlug', [
                'token' => $user->getToken(),
                'examSlug' => 'solid',
                'questionSlug' => 's_in_solid',
            ]);


        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertCount(2, $content['userQuestionList']);

        self::assertSame(1, $content['userQuestionList'][0]['userId']);
        self::assertSame(2, $content['userQuestionList'][1]['userId']);
    }

    public function testFindByExamAndQuestionSlugLoggedInNegativ(): void
    {
        $this->client->jsonRequest('POST',
            $this->apiUrl . '/api/login', [
                'username' => 'admin@cec.valantic.com',
                'password' => 'admin',
            ]
        );

        $user = $this->userRepository->find(2);

        $this->client->jsonRequest('GET',
            $this->apiUrl . '/api/userQuestion/findByExamAndQuestionSlug', [
                'token' => $user->getToken(),
            ]);


        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEmpty($content['userQuestionList']);
    }
}