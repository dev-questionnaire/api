<?php
declare(strict_types=1);

namespace App\Tests\Integration\Component\User\Business;

use App\Component\User\Business\FacadeInterface;
use App\Component\User\Persistence\EntityManager\UserEntityManagerInterface;
use App\Component\User\Persistence\Repository\UserRepositoryInterface;
use App\DataFixtures\UserFixtures;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FacadeFindByTokenTest extends KernelTestCase
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
        $userEntityManager = $container->get(UserEntityManagerInterface::class);

        $fixtures = $container->get(UserFixtures::class);
        $fixtures->load($this->entityManager);

        $userEntityManager->setToken('user@cec.valantic.com', 'token');
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

    public function testFindByTokenPositiv(): void
    {
        $userDataProvider = $this->facade->findByToken('token');

        self::assertSame(1, $userDataProvider->getId());
        self::assertSame('user@cec.valantic.com', $userDataProvider->getEmail());
        self::assertSame(['ROLE_USER'], $userDataProvider->getRoles());
        self::assertInstanceOf(\DateTime::class, $userDataProvider->getTokenTime());
    }

    public function testFindByTokenNegativ(): void
    {
        $userDataProvider = $this->facade->findByToken('test');

        self::assertNull($userDataProvider);
    }
}