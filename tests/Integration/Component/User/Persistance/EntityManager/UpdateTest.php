<?php
declare(strict_types=1);

namespace App\Tests\Integration\Component\User\Persistance\EntityManager;

use App\Component\User\Business\FacadeUserInterface;
use App\Component\User\Persistence\EntityManager\UserEntityManagerInterface;
use App\DataFixtures\UserFixtures;
use App\DataProvider\UserDataProvider;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UpdateTest extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;
    protected UserRepository $userRepository;
    protected UserEntityManagerInterface $userEntityManager;
    protected UserPasswordHasherInterface $userPasswordHasher;

    protected function setUp(): void
    {
        parent::setUp();

        $container = self::getContainer();

        $this->entityManager = self::getContainer()
            ->get('doctrine')
            ->getManager();

        $container->get(UserFixtures::class)->load($this->entityManager);

        $this->userEntityManager = $container->get(UserEntityManagerInterface::class);
        $this->userRepository = $container->get(UserRepository::class);
        $this->userPasswordHasher = $container->get(UserPasswordHasherInterface::class);
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

    public function testUpdatePositiv(): void
    {
        $user = $this->userRepository->find(1);
        self::assertSame('user@cec.valantic.com', $user->getEmail());

        $userDataProvider = new UserDataProvider();
        $userDataProvider
            ->setId(1)
            ->setEmail('test@cec.valantic.com')
            ->setPassword('Test_123!');

        $this->userEntityManager->update($userDataProvider);

        $user = $this->userRepository->find(1);

        self::assertSame('test@cec.valantic.com', $user->getEmail());
        self::assertSame(['ROLE_USER'], $user->getRoles());
        self::assertSame('test@cec.valantic.com', $user->getToken());
        self::assertTrue($this->userPasswordHasher->isPasswordValid($user, 'Test_123!'));
        self::assertNull($user->getTokenTime());
    }

    public function testUpdateNegativNoEmail(): void
    {
        $userDataProvider = new UserDataProvider();
        $userDataProvider
            ->setId(1)
            ->setPassword('Test_123!');

        $this->expectExceptionMessage("No data Provided");

        $this->userEntityManager->update($userDataProvider);
    }

    public function testUpdateNegativNoPassword(): void
    {
        $userDataProvider = new UserDataProvider();
        $userDataProvider
            ->setId(1)
            ->setEmail('test@email.com');

        $this->expectExceptionMessage("No data Provided");

        $this->userEntityManager->update($userDataProvider);
    }

    public function testUpdateNegativNoId(): void
    {
        $userDataProvider = new UserDataProvider();
        $userDataProvider
            ->setEmail('test@email.com')
            ->setPassword('test');

        $this->expectExceptionMessage("No data Provided");

        $this->userEntityManager->update($userDataProvider);
    }

    public function testUpdateNegativNoData(): void
    {
        $userDataProvider = new UserDataProvider();

        $this->expectExceptionMessage("No data Provided");

        $this->userEntityManager->update($userDataProvider);
    }

    public function testUpdateNegativUnserNotFound(): void
    {
        $userDataProvider = new UserDataProvider();
        $userDataProvider
            ->setId(100)
            ->setEmail('test@email.com')
            ->setPassword('test');

        $this->expectExceptionMessage("User not found");

        $this->userEntityManager->update($userDataProvider);
    }
}