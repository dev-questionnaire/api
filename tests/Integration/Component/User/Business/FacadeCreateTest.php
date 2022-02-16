<?php
declare(strict_types=1);

namespace App\Tests\Integration\Component\User\Business;

use App\Component\User\Business\FacadeInterface;
use App\DataFixtures\UserFixtures;
use App\DataProvider\UserDataProvider;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class FacadeCreateTest extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;
    protected UserRepository $userRepository;
    protected FacadeInterface $facade;
    protected UserPasswordHasherInterface $userPasswordHasher;

    protected function setUp(): void
    {
        parent::setUp();

        $container = self::getContainer();

        $this->entityManager = self::getContainer()
            ->get('doctrine')
            ->getManager();

        $container->get(UserFixtures::class)->load($this->entityManager);

        $this->facade = $container->get(FacadeInterface::class);
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
            ->setPassword('Test_123!')
            ->setVerificationPassword('Test_123!');

        $error = $this->facade->create($userDataProvider);

        self::assertEmpty($error);

        $user = $this->userRepository->find(3);

        self::assertSame('test@cec.valantic.com', $user->getEmail());
        self::assertSame(['ROLE_USER'], $user->getRoles());
        self::assertSame('test@cec.valantic.com', $user->getToken());
        self::assertTrue($this->userPasswordHasher->isPasswordValid($user, 'Test_123!'));
        self::assertNull($user->getTokenTime());
    }

    public function testCreateNegativEmail(): void
    {
        $userDataProvider = new UserDataProvider();
        $userDataProvider
            ->setPassword('Test_123!')
            ->setVerificationPassword('Test_123!');

        $error = $this->facade->create($userDataProvider);

        self::assertCount(1, $error);
        self::assertSame("No email provided", $error[0]);

        $userDataProvider = new UserDataProvider();
        $userDataProvider
            ->setEmail('user@cec.valantic.com')
            ->setPassword('Test_123!')
            ->setVerificationPassword('Test_123!');

        $error = $this->facade->create($userDataProvider);

        self::assertCount(1, $error);
        self::assertSame("Email is already taken", $error[0]);

        $userDataProvider = new UserDataProvider();
        $userDataProvider
            ->setEmail('user@email.com')
            ->setPassword('Test_123!')
            ->setVerificationPassword('Test_123!');

        $error = $this->facade->create($userDataProvider);

        self::assertCount(1, $error);
        self::assertSame("Email is not valid", $error[0]);
    }

    public function testCreateNegativPassword(): void
    {
        $userDataProvider = new UserDataProvider();
        $userDataProvider
            ->setEmail('test@cec.valantic.com');

        $error = $this->facade->create($userDataProvider);

        self::assertCount(1, $error);
        self::assertSame("No Password provided!", $error[0]);

        $userDataProvider = new UserDataProvider();
        $userDataProvider
            ->setEmail('test@cec.valantic.com')
            ->setPassword('test');

        $error = $this->facade->create($userDataProvider);

        self::assertCount(1, $error);
        self::assertSame("No Verification Password provided!", $error[0]);

        $userDataProvider = new UserDataProvider();
        $userDataProvider
            ->setEmail('test@cec.valantic.com')
            ->setPassword('test')
            ->setVerificationPassword('t');

        $error = $this->facade->create($userDataProvider);

        self::assertCount(5, $error);
        self::assertSame("Password too short!", $error[0]);
        self::assertSame("Password must include at least one number!", $error[1]);
        self::assertSame("Password must include at least one uppercase letter!", $error[2]);
        self::assertSame("Password must include at one special character!", $error[3]);
        self::assertSame("Password musst match Verification Password!", $error[4]);

        $userDataProvider = new UserDataProvider();
        $userDataProvider
            ->setEmail('test@cec.valantic.com')
            ->setPassword('TEST_123!')
            ->setVerificationPassword('TEST_123!');

        $error = $this->facade->create($userDataProvider);

        self::assertCount(1, $error);
        self::assertSame("Password must include at least one lowercase letter!", $error[0]);
    }
}