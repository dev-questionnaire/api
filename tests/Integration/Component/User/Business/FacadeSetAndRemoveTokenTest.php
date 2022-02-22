<?php
declare(strict_types=1);

namespace App\Tests\Integration\Component\User\Business;

use App\Component\User\Business\FacadeUserInterface;
use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FacadeSetAndRemoveTokenTest extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;
    protected UserRepository $userRepository;
    protected FacadeUserInterface $facade;

    protected function setUp(): void
    {
        parent::setUp();

        $container = self::getContainer();

        $this->entityManager = self::getContainer()
            ->get('doctrine')
            ->getManager();

        $userFixtures = $container->get(UserFixtures::class);
        $userFixtures->load($this->entityManager);

        $this->facade = $container->get(FacadeUserInterface::class);
        $this->userRepository = $container->get(UserRepository::class);
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

    public function testSetAndRemoveTokenPositiv(): void
    {
        $this->facade->setToken('user@cec.valantic.com', 'token');

        $user = $this->userRepository->find(1);
        $time = (new \DateTime("+ 60 Minutes"))->format("H:i");

        self::assertSame($time, $user->getTokenTime()->format("H:i"));
        self::assertSame('token', $user->getToken());

        $this->facade->removeToken('token');

        self::assertNull($user->getTokenTime());
        self::assertSame('user@cec.valantic.com', $user->getToken());
    }

    public function testSetTokenNegativ(): void
    {
        $this->expectExceptionMessage("User not found");

        $this->facade->setToken('test@email.com', 'token');
    }

    public function testRemoveTokenNegativ(): void
    {
        $this->expectExceptionMessage("User not found");

        $this->facade->removeToken('token');
    }
}