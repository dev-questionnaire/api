<?php
declare(strict_types=1);

namespace App\Tests\Acceptance\Component\Exam;

use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ExamControllerGetAllTest extends WebTestCase
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

    public function testGetAllNotLoggedIn(): void
    {
        $this->client->jsonRequest('GET',
            $this->apiUrl . '/api/exam/getAll', [
                'token' => null,
            ]);


        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertResponseStatusCodeSame(401);
        self::assertSame('access not authorized', $content['message']);
    }

    public function testGetAllLoggedIn(): void
    {
        $this->client->jsonRequest('POST',
            $this->apiUrl . '/api/login', [
                'username' => 'admin@cec.valantic.com',
                'password' => 'admin',
            ]
        );

        $user = $this->userRepository->find(2);

        $this->client->jsonRequest('GET',
            $this->apiUrl . '/api/exam/getAll', [
                'token' => $user->getToken(),
            ]);


        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertResponseIsSuccessful();

        $examList = $content['examList'];

        self::assertCount(4, $examList);
        self::assertSame('harun', $examList[0]['slug']);
        self::assertSame('oop', $examList[1]['slug']);
        self::assertSame('solid', $examList[2]['slug']);
        self::assertSame('testing', $examList[3]['slug']);
    }
}