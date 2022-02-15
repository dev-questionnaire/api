<?php
declare(strict_types=1);

namespace App\Tests\Integration\Repository;

use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserRepositoryTest extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;
    protected UserRepository $userRepository;
    protected UserPasswordHasherInterface $userPasswordHasher;

    protected function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();
        $container = self::getContainer();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->userRepository = $container->get(UserRepository::class);
        $this->userPasswordHasher = $container->get(UserPasswordHasherInterface::class);

        $userFixtures = $container->get(UserFixtures::class);

        $userFixtures->load($this->entityManager);
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

    public function testFindByEmailExcludeIdFound(): void
    {
        $result = $this->userRepository->findByEmailExcludeId('user@cec.valantic.com', 2);

        self::assertCount(1, $result);

        $result = $this->userRepository->findByEmailExcludeId('user@cec.valantic.com', 3);

        self::assertCount(1, $result);

        $user = $result[0];

        self::assertSame(1, $user->getId());
        self::assertSame('user@cec.valantic.com', $user->getEmail());
        self::assertSame('user@cec.valantic.com', $user->getUserIdentifier());
        self::assertSame(['ROLE_USER'], $user->getRoles());
        self::assertSame('user@cec.valantic.com', $user->getToken());
        self::assertNull($user->getTokenTime());
        self::assertTrue($this->userPasswordHasher->isPasswordValid($user, 'user'));
    }

    public function testFindByEmailExcludeIdEmpty(): void
    {
        $result = $this->userRepository->findByEmailExcludeId('test', 1);

        self::assertEmpty($result);

        $result = $this->userRepository->findByEmailExcludeId('user@cec.valantic.com', 1);

        self::assertEmpty($result);
    }
}