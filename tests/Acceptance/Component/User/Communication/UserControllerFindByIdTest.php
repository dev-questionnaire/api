<?php
declare(strict_types=1);

namespace App\Tests\Acceptance\Component\User\Communication;

use App\Component\User\Communication\UserController;
use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UserControllerFindByIdTest extends WebTestCase
{
    protected EntityManagerInterface $entityManager;
    protected UserController $controller;
    protected UserRepository $userRepository;
    protected HttpClientInterface $httpClient;
    protected string $apiUrl;
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();


        $this->client = static::createClient();
        $container = self::getContainer();

        $this->entityManager = self::getContainer()
            ->get('doctrine')
            ->getManager();

        $this->controller = $container->get(UserController::class);
        $this->userRepository = $container->get(UserRepository::class);
        $this->httpClient = $container->get(HttpClientInterface::class);

        $this->apiUrl = $container->get(ParameterBagInterface::class)->get('api.url');

        $fixtures = $container->get(UserFixtures::class);
        $fixtures->load($this->entityManager);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $connection = $this->entityManager->getConnection();

        $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 0');
        $connection->executeQuery('DELETE FROM user');
        $connection->executeQuery('ALTER TABLE user AUTO_INCREMENT=0');
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 1');

        $connection->close();
    }

    public function testGetByIdNotLoggedIn(): void
    {
        $this->client->jsonRequest('GET',
            $this->apiUrl . '/api/user/findById', [
                'id' => 1,
            ]);


        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertResponseStatusCodeSame(401);
        self::assertSame('access not authorized', $content['message']);
    }

    public function testGetByIdLoggedInPositiv(): void
    {
        $this->client->jsonRequest('POST',
            $this->apiUrl . '/api/login', [
                'username' => 'admin@cec.valantic.com',
                'password' => 'admin',
            ]
        );

        $user = $this->userRepository->find(2);

        $this->client->jsonRequest('GET',
            $this->apiUrl . '/api/user/findById', [
                'id' => 1,
                'token' => $user->getToken(),
            ]);

        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertResponseIsSuccessful('200');
        self::assertSame(1, $content['id']);
        self::assertSame('user@cec.valantic.com', $content['email']);
    }

    public function testGetByIdLoggedInNegativ(): void
    {
        $this->client->jsonRequest('POST',
            $this->apiUrl . '/api/login', [
                'username' => 'admin@cec.valantic.com',
                'password' => 'admin',
            ]
        );

        $user = $this->userRepository->find(2);

        $this->client->jsonRequest('GET',
            $this->apiUrl . '/api/user/findById', [
                'id' => 0,
                'token' => $user->getToken(),
            ]);

        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertResponseIsSuccessful('200');
        self::assertSame('no id provided', $content['message']);

        $this->client->jsonRequest('GET',
            $this->apiUrl . '/api/user/findById', [
                'id' => 100,
                'token' => $user->getToken(),
            ]);

        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertResponseIsSuccessful('200');
        self::assertSame('no user found', $content['message']);
    }
}