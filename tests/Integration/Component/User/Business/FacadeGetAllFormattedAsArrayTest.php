<?php
declare(strict_types=1);

namespace App\Tests\Integration\Component\User\Business;

use App\Component\User\Business\FacadeUserInterface;
use App\DataFixtures\UserFixtures;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FacadeGetAllFormattedAsArrayTest extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;
    protected FacadeUserInterface $facade;

    protected function setUp(): void
    {
        parent::setUp();

        $container = self::getContainer();

        $this->entityManager = self::getContainer()
            ->get('doctrine')
            ->getManager();

        $this->facade = $container->get(FacadeUserInterface::class);

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

    public function testFindAll(): void
    {
        $userDataProviderList = $this->facade->getAllFormattedAsArray();

        self::assertCount(2, $userDataProviderList);

        $userDataProvider = $userDataProviderList[1];

        self::assertSame(1, $userDataProvider['id']);
        self::assertSame('user@cec.valantic.com', $userDataProvider['email']);

        $userDataProvider = $userDataProviderList[2];

        self::assertSame(2, $userDataProvider['id']);
        self::assertSame('admin@cec.valantic.com', $userDataProvider['email']);
    }
}