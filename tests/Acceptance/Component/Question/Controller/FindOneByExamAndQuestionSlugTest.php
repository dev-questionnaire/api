<?php
declare(strict_types=1);

namespace App\Tests\Acceptance\Component\Question\Controller;

use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FindOneByExamAndQuestionSlugTest extends WebTestCase
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

    public function testFindOneByExamAndQuestionSlugNotLoggedIn(): void
    {
        $this->client->jsonRequest('GET',
            $this->apiUrl . '/api/question/findOneByExamAndQuestionSlug', [
                'token' => null,
            ]);


        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertResponseStatusCodeSame(401);
        self::assertSame('access not authorized', $content['message']);
    }

    public function testFindOneByExamAndQuestionSlugLoggedIn(): void
    {
        $this->client->jsonRequest('POST',
            $this->apiUrl . '/api/login', [
                'username' => 'admin@cec.valantic.com',
                'password' => 'admin',
            ]
        );

        $user = $this->userRepository->find(2);

        $this->client->jsonRequest('GET',
            $this->apiUrl . '/api/question/findOneByExamAndQuestionSlug', [
                'token' => $user->getToken(),
                'examSlug' => 'solid',
                'questionSlug' => 's_in_solid',
            ]);


        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertResponseIsSuccessful();

        self::assertSame('What does S in SOLID mean?', $content['question']['question']);
        self::assertSame([
            "Single possibility",
            "Single like a pringle",
            "Solid",
            "Sexy Programming",
        ], $content['question']['answers']);
    }

    public function testFindOneByExamAndQuestionSlugLoggedInNegativ(): void
    {
        $this->client->jsonRequest('POST',
            $this->apiUrl . '/api/login', [
                'username' => 'admin@cec.valantic.com',
                'password' => 'admin',
            ]
        );

        $user = $this->userRepository->find(2);

        $this->client->jsonRequest('GET',
            $this->apiUrl . '/api/question/findOneByExamAndQuestionSlug', [
                'token' => $user->getToken(),
            ]);


        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertResponseIsSuccessful();

        self::assertEmpty($content['question']);
    }
}