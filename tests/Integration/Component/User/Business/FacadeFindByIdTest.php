<?php
declare(strict_types=1);

namespace App\Tests\Integration\Component\User\Business;

use App\Component\User\Business\FacadeInterface;
use App\DataFixtures\UserFixtures;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FacadeFindByIdTest extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;
    protected FacadeInterface $facade;

    protected function setUp(): void
    {
        parent::setUp();

        $container = self::getContainer();

        $this->entityManager = self::getContainer()
            ->get('doctrine')
            ->getManager();

        $this->facade = $container->get(FacadeInterface::class);


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

    public function testFindByIdPositiv(): void
    {
        $userDataProvider = $this->facade->findById(1);

        self::assertSame(1, $userDataProvider->getId());
        self::assertSame('user@cec.valantic.com', $userDataProvider->getEmail());
        self::assertSame(['ROLE_USER'], $userDataProvider->getRoles());
        self::assertNull($userDataProvider->getTokenTime());
    }

    public function testFindByIdNegativ(): void
    {
        $userDataProvider = $this->facade->findById(100);

        self::assertNull($userDataProvider);
    }
}