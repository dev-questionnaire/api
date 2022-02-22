<?php
declare(strict_types=1);

namespace App\Tests\Integration\Component\User\Business;

use App\Component\User\Business\FacadeUserInterface;
use App\DataFixtures\UserFixtures;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FacadeCheckEmailTakenTest extends KernelTestCase
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

    public function testCheckEmailTakenPositiv(): void
    {
        $found = $this->facade->checkEmailTaken('admin@cec.valantic.com', 1);

        self::assertTrue($found);

        $found = $this->facade->checkEmailTaken('admin@cec.valantic.com', 100);

        self::assertTrue($found);
    }

    public function testCheckEmailTakenNegativ(): void
    {
        $found = $this->facade->checkEmailTaken('test@email.com', 1);

        self::assertFalse($found);

        $found = $this->facade->checkEmailTaken('test@email.com', 100);

        self::assertFalse($found);
    }
}