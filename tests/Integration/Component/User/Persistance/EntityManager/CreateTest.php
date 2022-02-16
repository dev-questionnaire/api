<?php
declare(strict_types=1);

namespace App\Tests\Integration\Component\User\Persistance\EntityManager;

use App\Component\User\Business\FacadeInterface;
use App\Component\User\Persistence\EntityManager\UserEntityManagerInterface;
use App\DataFixtures\UserFixtures;
use App\DataProvider\UserDataProvider;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateTest extends KernelTestCase
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

    public function testCreatePositiv(): void
    {
        $userDataProvider = new UserDataProvider();
        $userDataProvider
            ->setEmail('test@cec.valantic.com')
            ->setPassword('Test_123!');

        $this->userEntityManager->create($userDataProvider);

        $user = $this->userRepository->find(1);

        self::assertSame('test@cec.valantic.com', $user->getEmail());
        self::assertSame(['ROLE_USER'], $user->getRoles());
        self::assertSame('test@cec.valantic.com', $user->getToken());
        self::assertTrue($this->userPasswordHasher->isPasswordValid($user, 'Test_123!'));
        self::assertNull($user->getTokenTime());
    }

    public function testCreateNegativNoEmail(): void
    {
        $userDataProvider = new UserDataProvider();
        $userDataProvider
            ->setPassword('Test_123!');

        $this->expectExceptionMessage("No data Provided");

        $this->userEntityManager->create($userDataProvider);
    }

    public function testCreateNegativNoPassword(): void
    {
        $userDataProvider = new UserDataProvider();
        $userDataProvider
            ->setEmail('test@email.com');

        $this->expectExceptionMessage("No data Provided");

        $this->userEntityManager->create($userDataProvider);
    }

    public function testCreateNegativNoData(): void
    {
        $userDataProvider = new UserDataProvider();

        $this->expectExceptionMessage("No data Provided");

        $this->userEntityManager->create($userDataProvider);
    }
}